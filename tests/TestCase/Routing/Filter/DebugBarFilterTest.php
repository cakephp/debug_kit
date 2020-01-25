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
namespace DebugKit\Test\Routing\Filter;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest as Request;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Routing\Filter\DebugBarFilter;

/**
 * Test the debug bar
 */
class DebugBarFilterTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.DebugKit.Requests',
        'plugin.DebugKit.Panels',
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
        $this->skipIf($connection->getDriver() instanceof Sqlite, 'Schema insertion/removal breaks SQLite');
    }

    /**
     * Test loading panels.
     *
     * @return void
     */
    public function testSetupLoadingPanels()
    {
        $bar = new DebugBarFilter($this->events, []);
        $bar->setup();

        $this->assertContains('SqlLog', $bar->loadedPanels());
        $this->assertGreaterThan(1, $this->events->listeners('Controller.shutdown'));
        $this->assertInstanceOf('DebugKit\Panel\SqlLogPanel', $bar->panel('SqlLog'));
    }

    /**
     * Test that beforeDispatch call initialize on each panel
     *
     * @return void
     */
    public function testBeforeDispatch()
    {
        $bar = new DebugBarFilter($this->events, []);
        $bar->setup();

        $this->assertNull(Log::getConfig('debug_kit_log_panel'));
        $event = new Event('Dispatcher.beforeDispatch');
        $bar->beforeDispatch($event);

        $this->assertNotEmpty(Log::getConfig('debug_kit_log_panel'), 'Panel attached logger.');
    }

    /**
     * Test that afterDispatch ignores requestAction
     *
     * @return void
     */
    public function testAfterDispatchIgnoreRequestAction()
    {
        $request = new Request([
            'url' => '/articles',
            'params' => ['plugin' => null, 'requested' => 1],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new DebugBarFilter($this->events, []);
        $event = new Event('Dispatcher.afterDispatch', $bar, compact('request', 'response'));
        $this->assertNull($bar->afterDispatch($event));
        $this->assertNotContains('<script>', (string)$response->getBody());
    }

    /**
     * Test that afterDispatch ignores streaming bodies
     *
     * @return void
     */
    public function testAfterDispatchIgnoreStreamBodies()
    {
        $request = new Request([
            'url' => '/articles',
            'params' => ['plugin' => null],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
        ]);
        $response = $response->withStringBody('I am a teapot!');

        $bar = new DebugBarFilter($this->events, []);
        $event = new Event('Dispatcher.afterDispatch', $bar, compact('request', 'response'));
        $bar->afterDispatch($event);
        $this->assertEquals('I am a teapot!', $response->getBody());
    }

    /**
     * Test that afterDispatch saves panel data.
     *
     * @return void
     */
    public function testAfterDispatchSavesData()
    {
        $request = new Request([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new DebugBarFilter($this->events, []);
        $bar->setup();

        $event = new Event('Dispatcher.afterDispatch', $this, compact('request', 'response'));
        $response = $bar->afterDispatch($event);

        $requests = TableRegistry::get('DebugKit.Requests');
        $result = $requests->find()
            ->order(['Requests.requested_at' => 'DESC'])
            ->contain('Panels')
            ->first();

        $this->assertEquals('GET', $result->method);
        $this->assertEquals('/articles', $result->url);
        $this->assertNotEmpty($result->requested_at);
        $this->assertNotEmpty('text/html', $result->content_type);
        $this->assertSame(200, $result->status_code);
        $this->assertGreaterThan(1, $result->panels);

        $this->assertEquals('SqlLog', $result->panels[11]->panel);
        $this->assertEquals('DebugKit.sql_log_panel', $result->panels[11]->element);
        $this->assertSame('0', $result->panels[11]->summary);
        $this->assertEquals('Sql Log', $result->panels[11]->title);

        $timeStamp = filemtime(Plugin::path('DebugKit') . 'webroot' . DS . 'js' . DS . 'toolbar.js');

        $expected = '<html><title>test</title><body><p>some text</p>' .
            '<script id="__debug_kit" data-id="' . $result->id . '" ' .
            'data-url="http://localhost/" src="/debug_kit/js/toolbar.js?' . $timeStamp . '"></script>' .
            '</body>';
        $this->assertTextEquals($expected, $response->getBody());
    }

    /**
     * Test that afterDispatch does not modify response
     *
     * @return void
     */
    public function testAfterDispatchNoModifyResponse()
    {
        $request = new Request(['url' => '/articles']);

        $response = new Response([
            'statusCode' => 200,
            'type' => 'application/json',
            'body' => '{"some":"json"}',
        ]);

        $bar = new DebugBarFilter($this->events, []);
        $bar->setup();

        $event = new Event('Dispatcher.afterDispatch', $bar, compact('request', 'response'));
        $bar->afterDispatch($event);
        $this->assertTextEquals('{"some":"json"}', $response->getBody());
    }

    /**
     * test isEnabled responds to debug flag.
     *
     * @return void
     */
    public function testIsEnabled()
    {
        Configure::write('debug', true);
        $bar = new DebugBarFilter($this->events, []);
        $this->assertTrue($bar->isEnabled(), 'debug is on, panel is enabled');

        Configure::write('debug', false);
        $bar = new DebugBarFilter($this->events, []);
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
        $bar = new DebugBarFilter($this->events, ['forceEnable' => true]);
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
        $bar = new DebugBarFilter($this->events, [
            'forceEnable' => function () {
                return true;
            },
        ]);
        $this->assertTrue($bar->isEnabled(), 'debug is off, panel is forced on');
    }
}
