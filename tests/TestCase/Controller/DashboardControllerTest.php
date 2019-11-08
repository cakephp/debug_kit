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
 * @since         3.19.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use DebugKit\TestApp\Application;

/**
 * Dashboard controller test.
 */
class DashboardControllerTest extends IntegrationTestCase
{
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

    public function testIndexNoRequests()
    {
        $requests = TableRegistry::get('DebugKit.Requests');
        $requests->Panels->deleteAll('1=1');
        $requests->deleteAll('1=1');

        $this->get('/debug-kit/dashboard');

        $this->assertResponseOk();
        $this->assertResponseContains('Database');
        $this->assertResponseNotContains('Reset database');
    }

    public function testIndexWithRequests()
    {
        $requests = TableRegistry::get('DebugKit.Requests');
        $request = $requests->newEntity(['url' => '/example']);
        $requests->save($request);

        $this->get('/debug-kit/dashboard');

        $this->assertResponseOk();
        $this->assertResponseContains('Database');
        $this->assertResponseContains('Reset database');
    }

    public function testReset()
    {
        $requests = TableRegistry::get('DebugKit.Requests');
        $this->assertGreaterThan(0, $requests->find()->count(), 'precondition failed');

        $this->post('/debug-kit/dashboard/reset');

        $this->assertRedirect('/debug-kit');
        $this->assertEquals(0, $requests->find()->count());
    }
}
