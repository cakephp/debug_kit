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
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DebugKit\Test\TestCase\FixtureFactoryTrait;
use DebugKit\TestApp\Application;

/**
 * Panel controller test.
 */
class PanelsControllerTest extends TestCase
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
        $request = $this->makeRequest();
        $this->makePanel($request);
        $this->get("/debug-kit/panels/{$request->id}");

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
        $request = $this->makeRequest();
        $panel = $this->makePanel($request);

        $this->get("/debug-kit/panels/view/{$panel->id}");

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

    /**
     * @return void
     */
    public function testLatestHistory()
    {
        $request = $this->getTableLocator()->get('DebugKit.Requests')->find('recent')->first();
        if (!$request) {
            $request = $this->makeRequest();
        }
        $panel = $this->makePanel($request, 'DebugKit.History', 'History');

        $this->get('/debug-kit/panels/view/latest-history');
        $this->assertRedirect([
            'action' => 'view', $panel->id,
        ]);
    }
}
