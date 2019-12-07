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
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestCase;
use DebugKit\TestApp\Application;

/**
 * Toolbar controller test.
 */
class ToolbarControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.DebugKit.Requests',
        'plugin.DebugKit.Panels',
    ];

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
        $this->useHttpServer(true);
    }

    /**
     * Test clearing the cache does not work with GET
     *
     * @return void
     */
    public function testClearCacheNoGet()
    {
        $this->get('/debug-kit/toolbar/clear-cache?name=testing');
        $this->assertResponseCode(405);
    }

    /**
     * Test clearing the cache.
     *
     * @return void
     */
    public function testClearCache()
    {
        $mock = $this->getMockBuilder('Cake\Cache\CacheEngine')->getMock();
        $mock->expects($this->once())
            ->method('init')
            ->will($this->returnValue(true));
        $mock->expects($this->once())
            ->method('clear')
            ->will($this->returnValue(true));
        Cache::setConfig('testing', $mock);

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/debug-kit/toolbar/clear-cache', ['name' => 'testing']);
        $this->assertResponseOk();
        $this->assertResponseContains('success');
    }
}
