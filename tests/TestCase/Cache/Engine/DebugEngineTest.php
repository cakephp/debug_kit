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
     * @var CacheEngine
     */
    private $mock;

    /**
     * setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $mock = $this->getMockBuilder('Cake\Cache\CacheEngine')->getMock();
        $this->mock = $mock;
        $this->mock->method('getMultiple')->will($this->returnValue([]));

        $this->engine = new DebugEngine($mock);
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
        ]);
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
        ]);
        $engine->init();
    }

    /**
     * Test that methods are proxied.
     *
     * @return void
     */
    public function testProxyMethodsTracksMetrics()
    {
        $this->mock->expects($this->at(0))
            ->method('get');
        $this->mock->expects($this->at(1))
            ->method('set');
        $this->mock->expects($this->at(2))
            ->method('delete');
        $this->mock->expects($this->at(3))
            ->method('increment');
        $this->mock->expects($this->at(4))
            ->method('decrement');

        $this->engine->get('key');
        $this->engine->set('key', 'value');
        $this->engine->delete('key');
        $this->engine->increment('key');
        $this->engine->decrement('key');

        $result = $this->engine->metrics();
        $this->assertEquals(3, $result['set']);
        $this->assertEquals(1, $result['delete']);
        $this->assertEquals(1, $result['get']);
    }

    /**
     * Test that methods are proxied.
     *
     * @return void
     */
    public function testProxyMethodsTimers()
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

        $result = DebugTimer::getAll();
        $this->assertCount(10, $result);
        $this->assertArrayHasKey('Cache.get key', $result);
        $this->assertArrayHasKey('Cache.set key', $result);
        $this->assertArrayHasKey('Cache.delete key', $result);
        $this->assertArrayHasKey('Cache.increment key', $result);
        $this->assertArrayHasKey('Cache.decrement key', $result);
        $this->assertArrayHasKey('Cache.getMultiple', $result);
        $this->assertArrayHasKey('Cache.setMultiple', $result);
        $this->assertArrayHasKey('Cache.deleteMultiple', $result);
        $this->assertArrayHasKey('Cache.clearGroup group', $result);
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
            'groups' => ['test', 'test2'],
        ]);
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
        ]);
        $engine->init();

        $data = $engine->getConfig();
        $this->assertArrayHasKey('path', $data);
        $this->assertArrayHasKey('isWindows', $data);
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
        ]);
        $this->assertEquals('File', (string)$engine);
    }
}
