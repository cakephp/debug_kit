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
namespace DebugKit\Test\TestCase\Middleware;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Http\CallbackStream;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use DebugKit\Middleware\DebugKitMiddleware;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Test the middleware object
 */
class DebugKitMiddlewareTest extends TestCase
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
     * setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $connection = ConnectionManager::get('test');
        $this->skipIf($connection->getDriver() instanceof Sqlite, 'Schema insertion/removal breaks SQLite');
        $this->oldConfig = Configure::read('DebugKit');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::write('DebugKit', $this->oldConfig);
    }

    protected function handler()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        return $handler;
    }

    /**
     * Ensure data is saved for HTML requests
     *
     * @return void
     */
    public function testInvokeSaveData()
    {
        $request = new ServerRequest([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
            'body' => '<html><title>test</title><body><p>some text</p></body>',
        ]);

        $handler = $this->handler();
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $middleware = new DebugKitMiddleware();
        $response = $middleware->process($request, $handler);
        $this->assertInstanceOf(Response::class, $response, 'Should return the response');

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
        $this->assertNotNull($result->panels[11]->summary);
        $this->assertSame('Sql Log', $result->panels[11]->title);

        $timeStamp = filemtime(Plugin::path('DebugKit') . 'webroot' . DS . 'js' . DS . 'toolbar.js');

        $expected = '<html><title>test</title><body><p>some text</p>' .
            '<script id="__debug_kit" data-id="' . $result->id . '" ' .
            'data-url="http://localhost/" src="/debug_kit/js/toolbar.js?' . $timeStamp . '"></script>' .
            '</body>';
        $body = (string)$response->getBody();
        $this->assertTextEquals($expected, $body);
    }

    /**
     * Ensure that streaming results are tracked, but not modified.
     *
     * @return void
     */
    public function testInvokeNoModifyBinaryResponse()
    {
        $request = new ServerRequest([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/html',
        ]);

        $handler = $this->handler();
        $handler->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function ($req) use ($response) {
                $stream = new CallbackStream(function () {
                    return 'hi!';
                });

                return $response->withBody($stream);
            }));
        $middleware = new DebugKitMiddleware();
        $result = $middleware->process($request, $handler);
        $this->assertInstanceOf(Response::class, $result, 'Should return a response');

        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $total = $requests->find()->where(['url' => '/articles'])->count();

        $this->assertSame(1, $total, 'Should track response');
        $body = $result->getBody();
        $this->assertStringNotContainsString('__debug_kit', '' . $body);
        $this->assertStringNotContainsString('<script', '' . $body);
    }

    /**
     * Ensure that no script tag is added to non html responses.
     *
     * @return void
     */
    public function testInvokeNoModifyNonHtmlResponse()
    {
        $request = new ServerRequest([
            'url' => '/articles',
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $response = new Response([
            'statusCode' => 200,
            'type' => 'text/plain',
            'body' => 'OK',
        ]);

        $handler = $this->handler();
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);
        $middleware = new DebugKitMiddleware();
        $result = $middleware->process($request, $handler);
        $this->assertInstanceOf(Response::class, $result, 'Should return a response');

        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $total = $requests->find()->where(['url' => '/articles'])->count();

        $this->assertSame(1, $total, 'Should track response');
        $body = (string)$result->getBody();
        $this->assertSame('OK', $body);
    }

    /**
     * Test that configuration is correctly passed to the service
     *
     * @return void
     */
    public function testConfigIsPassed()
    {
        $config = ['foo' => 'bar'];
        Configure::write('DebugKit', $config);
        $layer = new DebugKitMiddleware();
        $prop = new \ReflectionProperty(DebugKitMiddleware::class, 'service');
        $prop->setAccessible(true);
        $service = $prop->getValue($layer);
        $this->assertSame('bar', $service->getConfig('foo'));
    }
}
