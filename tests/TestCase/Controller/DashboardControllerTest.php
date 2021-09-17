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
 * @since         3.19.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DebugKit\Test\TestCase\FixtureFactoryTrait;
use DebugKit\TestApp\Application;

/**
 * Dashboard controller test.
 */
class DashboardControllerTest extends TestCase
{
    use FixtureFactoryTrait;
    use IntegrationTestTrait;

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
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
    }

    public function testIndexNoRequests()
    {
        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $requests->Panels->deleteAll('1=1');
        $requests->deleteAll('1=1');

        $this->get('/debug-kit/dashboard');

        $this->assertResponseOk();
        $this->assertResponseContains('Database');
        $this->assertResponseNotContains('Reset database');
    }

    public function testIndexWithRequests()
    {
        $request = $this->makeRequest();
        $this->makePanel($request);

        $this->get('/debug-kit/dashboard');

        $this->assertResponseOk();
        $this->assertResponseContains('Database');
        $this->assertResponseContains('Reset database');
    }

    public function testReset()
    {
        $request = $this->makeRequest();
        $this->makePanel($request);

        $this->post('/debug-kit/dashboard/reset');

        $this->assertRedirect('/debug-kit');
        $requests = $this->getTableLocator()->get('DebugKit.Requests');
        $this->assertSame(0, $requests->find()->count());
    }
}
