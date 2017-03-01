<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test;

use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Model\Entity\Request as RequestEntity;
use DebugKit\ToolbarService;

/**
 * Test the debug bar
 */
class ToolbarServiceTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.debug_kit.requests',
        'plugin.debug_kit.panels'
    ];

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->events = new EventManager();

        $connection = ConnectionManager::get('test');
        $this->skipIf($connection->driver() instanceof Sqlite, 'Schema insertion/removal breaks SQLite');
    }

    /**
     * Test loading panels.
     *
     * @return void
     */
    public function testLoadPanels()
    {
        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();

        $this->assertContains('SqlLog', $bar->loadedPanels());
        $this->assertGreaterThan(1, $this->events->listeners('Controller.shutdown'));
        $this->assertInstanceOf('DebugKit\Panel\SqlLogPanel', $bar->panel('SqlLog'));
    }

    /**
     * Test disabling panels.
     *
     * @return void
     */
    public function testDisablePanels()
    {
        $bar = new ToolbarService($this->events, ['panels' => [
            'DebugKit.SqlLog' => false,
            'DebugKit.Cache' => true,
            'DebugKit.Session'
        ]]);
        $bar->loadPanels();

        $this->assertNotContains('SqlLog', $bar->loadedPanels());
        $this->assertContains('Cache', $bar->loadedPanels());
        $this->assertContains('Session', $bar->loadedPanels());
    }

    /**
     * Test that beforeDispatch call initialize on each panel
     *
     * @return void
     */
    public function testInitializePanels()
    {
        Log::drop('debug_kit_log_panel');
        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();

        $this->assertNull(Log::config('debug_kit_log_panel'));
        $bar->initializePanels();

        $this->assertNotEmpty(Log::config('debug_kit_log_panel'), 'Panel attached logger.');
    }

    /**
     * Test that saveData ignores requestAction
     *
     * @return void
     */
    public function testSaveDataIgnoreRequestAction()
    {
        $request = new Request([
            'url' => '/articles',
            'params' => ['plugin' => null, 'requested' => 1]
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>'
        ]);

        $bar = new ToolbarService($this->events, []);
        $this->assertNull($bar->saveData($request, $response));
    }

    /**
     * Test that saveData works
     *
     * @return void
     */
    public function testSaveData()
    {
        $request = new Request([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET']
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>'
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();
        $row = $bar->saveData($request, $response);
        $this->assertNotEmpty($row);

        $requests = TableRegistry::get('DebugKit.Requests');
        $result = $requests->find()
            ->order(['Requests.requested_at' => 'DESC'])
            ->contain('Panels')
            ->first();

        $this->assertEquals('GET', $result->method);
        $this->assertEquals('/articles', $result->url);
        $this->assertNotEmpty($result->requested_at);
        $this->assertNotEmpty('text/html', $result->content_type);
        $this->assertEquals(200, $result->status_code);
        $this->assertGreaterThan(1, $result->panels);

        $this->assertEquals('SqlLog', $result->panels[10]->panel);
        $this->assertEquals('DebugKit.sql_log_panel', $result->panels[10]->element);
        $this->assertSame('0', $result->panels[10]->summary);
        $this->assertEquals('Sql Log', $result->panels[10]->title);
    }

    /**
     * Test injectScripts()
     *
     * @return void
     */
    public function testInjectScriptsLastBodyTag()
    {
        $request = new Request([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET']
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>'
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();
        $row = $bar->saveData($request, $response);
        $response = $bar->injectScripts($row, $response);

        $expected = '<html><title>test</title><body><p>some text</p>' .
            '<script id="__debug_kit" data-id="' . $row->id . '" ' .
            'data-url="http://localhost/" src="/debug_kit/js/toolbar.js"></script>' .
            '</body>';
        $this->assertTextEquals($expected, $response->body());
    }

    /**
     * Test that saveData ignores streaming bodies
     *
     * @return void
     */
    public function testInjectScriptsStreamBodies()
    {
        $request = new Request([
            'url' => '/articles',
            'params' => ['plugin' => null]
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
        ]);
        $response->body(function () {
            return 'I am a teapot!';
        });

        $bar = new ToolbarService($this->events, []);
        $row = new RequestEntity(['id' => 'abc123']);

        $result = $bar->injectScripts($row, $response);
        $this->assertInstanceOf('Cake\Network\Response', $result);
        if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
            $this->assertEquals('I am a teapot!', $response->body());
        } else {
            $this->assertInstanceOf('Closure', $response->body());
        }
    }

    /**
     * Test that afterDispatch does not modify response
     *
     * @return void
     */
    public function testInjectScriptsNoModifyResponse()
    {
        $request = new Request(['url' => '/articles']);

        $response = new Response([
            'statusCode' => 200,
            'type' => 'application/json',
            'body' => '{"some":"json"}'
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();

        $row = $bar->saveData($request, $response);
        $response = $bar->injectScripts($row, $response);
        $this->assertTextEquals('{"some":"json"}', $response->body());
    }

    /**
     * test isEnabled responds to debug flag.
     *
     * @return void
     */
    public function testIsEnabled()
    {
        Configure::write('debug', true);
        $bar = new ToolbarService($this->events, []);
        $this->assertTrue($bar->isEnabled(), 'debug is on, panel is enabled');

        Configure::write('debug', false);
        $bar = new ToolbarService($this->events, []);
        $this->assertFalse($bar->isEnabled(), 'debug is off, panel is disabled');
    }

    /**
     * test isEnabled responds to forceEnable config flag.
     *
     * @return void
     */
    public function testIsEnabledForceEnable()
    {
        Configure::write('debug', false);
        $bar = new ToolbarService($this->events, ['forceEnable' => true]);
        $this->assertTrue($bar->isEnabled(), 'debug is off, panel is forced on');
    }

    /**
     * test isEnabled responds to forceEnable callable.
     *
     * @return void
     */
    public function testIsEnabledForceEnableCallable()
    {
        Configure::write('debug', false);
        $bar = new ToolbarService($this->events, [
            'forceEnable' => function () {
                return true;
            }
        ]);
        $this->assertTrue($bar->isEnabled(), 'debug is off, panel is forced on');
    }
}
