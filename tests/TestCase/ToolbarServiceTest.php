<?php
declare(strict_types=1);

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
namespace DebugKit\Test\TestCase;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest as Request;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use DebugKit\Model\Entity\Request as RequestEntity;
use DebugKit\ToolbarService;

/**
 * Test the debug bar
 */
class ToolbarServiceTest extends TestCase
{
    /**
     * Tables to reset each test.
     *
     * @var array<string>
     */
    protected $fixtures = [
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
    public function setUp(): void
    {
        parent::setUp();
        $this->events = new EventManager();

        $connection = ConnectionManager::get('test');
        $this->skipIf($connection->getDriver() instanceof Sqlite, 'Schema insertion/removal breaks SQLite');
    }

    /**
     * teardown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        putenv('HTTP_HOST=');
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
            'DebugKit.Session',
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

        $this->assertNull(Log::getConfig('debug_kit_log_panel'));
        $bar->initializePanels();

        $this->assertNotEmpty(Log::getConfig('debug_kit_log_panel'), 'Panel attached logger.');
    }

    /**
     * Test that saveData ignores debugkit paths
     *
     * @return void
     */
    public function testSaveDataIgnoreDebugKit()
    {
        $request = new Request([
            'url' => '/debug_kit/panel/abc123',
            'params' => [],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $this->assertFalse($bar->saveData($request, $response));
    }

    /**
     * Test that saveData ignores debugkit paths
     *
     * @return void
     */
    public function testSaveDataIgnoreDebugKitDashedUrl()
    {
        $request = new Request([
            'url' => '/debug-kit/panel/abc123',
            'params' => [],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $this->assertFalse($bar->saveData($request, $response));

        $request = new Request([
            'url' => '/debug-kit',
            'params' => [],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $this->assertNotEmpty($bar->saveData($request, $response));
    }

    /**
     * Test that saveData ignores path that matches "ignorePaths" regex.
     *
     * @return void
     */
    public function testSaveDataIgnorePaths()
    {
        $request = new Request([
            'url' => '/foo.jpg',
            'params' => [],
        ]);
        $response = new Response([
            'status' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, ['ignorePathsPattern' => '/\.(jpg|png|gif)$/']);
        $this->assertFalse($bar->saveData($request, $response));

        $request = new Request([
            'url' => '/foo.jpg',
            'params' => [],
        ]);
        $response = new Response([
            'status' => 404,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, ['ignorePathsPattern' => '/\.(jpg|png|gif)$/']);
        $this->assertNotEmpty($bar->saveData($request, $response));
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
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();
        $row = $bar->saveData($request, $response);
        $this->assertNotEmpty($row);

        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $result = $requests->find()
            ->order(['Requests.requested_at' => 'DESC'])
            ->contain('Panels')
            ->first();

        $this->assertSame('GET', $result->method);
        $this->assertSame('/articles', $result->url);
        $this->assertNotEmpty($result->requested_at);
        $this->assertNotEmpty('text/html', $result->content_type);
        $this->assertSame(200, $result->status_code);
        $this->assertGreaterThan(1, $result->panels);

        $this->assertSame('SqlLog', $result->panels[11]->panel);
        $this->assertSame('DebugKit.sql_log_panel', $result->panels[11]->element);
        $this->assertSame('0', $result->panels[11]->summary);
        $this->assertSame('Sql Log', $result->panels[11]->title);
    }

    /**
     * Test that saveData gracefully handles missing connections
     *
     * @return void
     */
    public function testSaveDataMissingConnection()
    {
        $restore = ConnectionManager::getConfig('test_debug_kit');
        ConnectionManager::drop('test_debug_kit');

        $request = new Request([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();
        $row = $bar->saveData($request, $response);
        $this->assertEmpty($row);

        ConnectionManager::setConfig('test_debug_kit', $restore);
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
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();
        $row = $bar->saveData($request, $response);
        $response = $bar->injectScripts($row, $response);

        $timeStamp = filemtime(Plugin::path('DebugKit') . 'webroot' . DS . 'js' . DS . 'toolbar.js');

        $expected = '<html><title>test</title><body><p>some text</p>' .
            '<script id="__debug_kit" data-id="' . $row->id . '" ' .
            'data-url="http://localhost/" src="/debug_kit/js/toolbar.js?' . $timeStamp . '"></script>' .
            '</body>';
        $this->assertTextEquals($expected, (string)$response->getBody());
        $this->assertTrue($response->hasHeader('X-DEBUGKIT-ID'), 'Should have a tracking id');
    }

    /**
     * Test that saveData ignores file bodies.
     *
     * @return void
     */
    public function testInjectScriptsFileBodies()
    {
        $request = new Request([
            'url' => '/articles',
            'params' => ['plugin' => null],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
        ]);
        $response = $response->withFile(__FILE__);

        $bar = new ToolbarService($this->events, []);
        $row = new RequestEntity(['id' => 'abc123']);

        $result = $bar->injectScripts($row, $response);
        $this->assertInstanceOf('Cake\Http\Response', $result);
        $this->assertSame(file_get_contents(__FILE__), '' . $result->getBody());
        $this->assertTrue($result->hasHeader('X-DEBUGKIT-ID'), 'Should have a tracking id');
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
            'params' => ['plugin' => null],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
        ]);
        $response = $response->withStringBody('I am a teapot!');

        $bar = new ToolbarService($this->events, []);
        $row = new RequestEntity(['id' => 'abc123']);

        $result = $bar->injectScripts($row, $response);
        $this->assertInstanceOf('Cake\Http\Response', $result);
        $this->assertSame('I am a teapot!', (string)$response->getBody());
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
            'body' => '{"some":"json"}',
        ]);

        $bar = new ToolbarService($this->events, []);
        $bar->loadPanels();

        $row = $bar->saveData($request, $response);
        $response = $bar->injectScripts($row, $response);
        $this->assertTextEquals('{"some":"json"}', (string)$response->getBody());
        $this->assertTrue($response->hasHeader('X-DEBUGKIT-ID'), 'Should have a tracking id');
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
     * Test isEnabled returns false for some suspiciously production environments
     *
     * @param string $domain The domain name where the app is hosted
     * @param bool $isEnabled The expectation for isEnabled()
     * @dataProvider domainsProvider
     * @return void
     */
    public function testIsEnabledProductionEnv($domain, $isEnabled)
    {
        Configure::write('debug', true);
        putenv("HTTP_HOST=$domain");
        $bar = new ToolbarService($this->events, []);
        $this->assertSame($isEnabled, $bar->isEnabled());

        $bar = new ToolbarService($this->events, ['forceEnable' => true]);
        $this->assertTrue($bar->isEnabled(), 'When forced should always be on.');
    }

    public function domainsProvider()
    {
        return [
            ['localhost', true],
            ['192.168.1.34', true],
            ['127.0.0.1:8765', true],
            ['10.14.34.5', true],
            ['10.14.34.5:80', true],
            ['myapp.localhost', true],
            ['myapp.local', true],
            ['myapp.dev', false],
            ['myapp', true],
            ['myapp.invalid', true],
            ['myapp.test', true],
            ['myapp.com', false],
            ['myapp.io', false],
            ['myapp.net', false],
            ['172.18.0.10', true],
            ['172.112.34.2', false],
            ['6.112.34.2', false],
            ['[abcd::]', false], // public
            ['[fc00::]', true], // private
            ['[::1]', true], // localhost
        ];
    }

    /**
     * Tests isEnabled() with custom safe TLD.
     *
     * @return void
     */
    public function testIsEnabledProductionEnvCustomTld()
    {
        $domain = 'myapp.foobar';
        Configure::write('debug', true);

        putenv("HTTP_HOST=$domain");
        $bar = new ToolbarService($this->events, []);
        $this->assertFalse($bar->isEnabled());

        $bar = new ToolbarService($this->events, ['safeTld' => ['foobar']]);
        $this->assertTrue($bar->isEnabled(), 'When safe TLD should always be on.');
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
            },
        ]);
        $this->assertTrue($bar->isEnabled(), 'debug is off, panel is forced on');
    }
}
