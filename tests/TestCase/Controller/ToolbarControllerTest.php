<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DebugKit\TestApp\Application;

/**
 * Toolbar controller test.
 */
class ToolbarControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
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
            ->willReturn(true);
        $mock->expects($this->once())
            ->method('clear')
            ->willReturn(true);
        Cache::setConfig('testing', $mock);

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/debug-kit/toolbar/clear-cache', ['name' => 'testing']);
        $this->assertResponseOk();
        $this->assertResponseContains('success');
    }

    /**
     * Test clearing the session does not work with GET
     *
     * @return void
     */
    public function testClearSessionNoGet()
    {
        $this->get('/debug-kit/toolbar/clear-session');
        $this->assertResponseCode(405);
    }

    /**
     * Test clearing the session.
     *
     * @return void
     */
    public function testClearSession()
    {
        $this->session(['test' => 'value']);
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/debug-kit/toolbar/clear-session');
        $this->assertResponseOk();
        $this->assertResponseContains('success');
        $this->assertResponseContains('Session cleared');
    }
}
