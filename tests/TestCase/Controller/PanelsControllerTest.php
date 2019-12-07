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

use Cake\TestSuite\IntegrationTestCase;
use DebugKit\TestApp\Application;

/**
 * Panel controller test.
 */
class PanelsControllerTest extends IntegrationTestCase
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
     * tests index page returns as JSON
     *
     * @return void
     */
    public function testIndex()
    {
        $this->configRequest([
            'headers' => [
                'accept' => 'application/json, text/javascript, */*; q=0.01',
            ],
        ]);

        $this->get('/debug-kit/panels/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa');

        $this->assertResponseOk();
        $this->assertContentType('application/json');
    }

    /**
     * Test getting a panel that exists.
     *
     * @return void
     */
    public function testView()
    {
        $this->get('/debug-kit/panels/view/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa');

        $this->assertResponseOk();
        $this->assertResponseContains('Request</h2>');
        $this->assertResponseContains('Routing Params</h4>');
    }

    /**
     * Test getting a panel that does notexists.
     *
     * @return void
     */
    public function testViewNotExists()
    {
        $this->get('/debug-kit/panels/view/aaaaaaaa-ffff-ffff-ffff-aaaaaaaaaaaa');
        $this->assertResponseError();
        $this->assertResponseContains('Error page');
    }
}
