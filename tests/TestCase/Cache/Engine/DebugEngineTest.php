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
 * @since         0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Cache\Engine;

use BadMethodCallException;
use Cake\Cache\Engine\ArrayEngine;
use Cake\Log\Engine\ArrayLog;
use Cake\TestSuite\TestCase;
use DebugKit\Cache\Engine\DebugEngine;
use DebugKit\DebugTimer;

/**
 * Class DebugEngine
 */
class DebugEngineTest extends TestCase
{
    /**
     * @var DebugEngine
     */
    protected $engine;

    /**
     * @var \Cake\Cache\Engine\ArrayEngine
     */
    private $wrapped;

    /**
     * @var \Cake\Log\Engine\ArrayLog
     */
    private $logger;

    /**
     * setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->wrapped = new ArrayEngine();
        $this->wrapped->init(['prefix' => '']);

        $this->logger = new ArrayLog();

        $this->engine = new DebugEngine($this->wrapped, 'test', $this->logger);
        $this->engine->init();
        DebugTimer::clear();
    }

    /**
     * Test that init() builds engines based on config.
     *
     * @return void
     */
    public function testInitEngineBasedOnConfig()
    {
        $engine = new DebugEngine([
            'className' => 'File',
            'path' => TMP,
            'prefix' => '',
        ], 'test', $this->logger);
        $this->assertTrue($engine->init());
        $this->assertInstanceOf('Cake\Cache\Engine\FileEngine', $engine->engine());
    }

    /**
     * Test that the normal errors bubble up still.
     *
     * @return void
     */
    public function testInitErrorOnInvalidConfig()
    {
        $this->expectException(BadMethodCallException::class);
        $engine = new DebugEngine([
            'className' => 'Derpy',
            'path' => TMP,
            'prefix' => '',
        ], 'test', $this->logger);
        $engine->init();
    }

    /**
     * Test that methods are proxied.
     *
     * @return void
     */
    public function testProxyMethodsTracksMetrics()
    {
        $this->engine->get('key');
        $this->engine->set('key', 'value');
        $this->engine->get('key');
        $this->engine->delete('key');
        $this->engine->increment('key');
        $this->engine->decrement('key');

        $result = $this->engine->metrics();
        $this->assertSame(3, $result['set']);
        $this->assertSame(1, $result['delete']);
        $this->assertSame(1, $result['get miss']);
        $this->assertSame(1, $result['get hit']);
    }

    /**
     * Test that methods are proxied.
     *
     * @return void
     */
    public function testProxyMethodLogs()
    {
        $this->engine->get('key');
        $this->engine->set('key', 'value');
        $this->engine->delete('key');
        $this->engine->increment('key');
        $this->engine->decrement('key');
        $this->engine->setMultiple(['key' => 'value']);
        $this->engine->getMultiple(['key']);
        $this->engine->deleteMultiple(['key']);
        $this->engine->clearGroup('group');

        $logs = $this->logger->read();
        $this->assertCount(9, $logs);
        $this->assertStringStartsWith('info: :test: get `key`', $logs[0]);
        $this->assertStringStartsWith('info: :test: set `key`', $logs[1]);
    }

    /**
     * Test that groups proxies
     *
     * @return void
     */
    public function testGroupsProxies()
    {
        $engine = new DebugEngine([
            'className' => 'File',
            'path' => TMP,
            'prefix' => '',
            'groups' => ['test', 'test2'],
        ], 'test', $this->logger);
        $engine->init();
        $result = $engine->groups();
        $this->assertEquals(['test', 'test2'], $result);
    }

    /**
     * Test that config methods proxy the config data.
     *
     * @return void
     */
    public function testConfigProxies()
    {
        $engine = new DebugEngine([
            'className' => 'File',
            'path' => TMP,
            'prefix' => '',
        ], 'test', $this->logger);
        $engine->init();

        $data = $engine->getConfig();
        $this->assertArrayHasKey('path', $data);
        $this->assertArrayHasKey('prefix', $data);
    }

    /**
     * Test to string
     *
     * @return void
     */
    public function testToString()
    {
        $engine = new DebugEngine([
            'className' => 'File',
            'path' => TMP,
            'groups' => ['test', 'test2'],
        ], 'test', $this->logger);
        $this->assertSame('File', (string)$engine);
    }
}
