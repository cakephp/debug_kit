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
    protected array $fixtures = [
        'plugin.DebugKit.Requests',
        'plugin.DebugKit.Panels',
    ];

    /**
     * Setup method.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->configApplication(Application::class, []);
    }

    /**
     * tests index page returns as JSON
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->configRequest([
            'headers' => [
                'accept' => 'application/json, text/javascript, */*; q=0.01',
            ],
        ]);
        $request = $this->makeRequest();
        $this->makePanel($request);
        $this->get('/debug-kit/panels/' . $request->id);

        $this->assertResponseOk();
        $this->assertContentType('application/json');
    }

    /**
     * Test getting a panel that exists.
     *
     * @return void
     */
    public function testView(): void
    {
        $request = $this->makeRequest();
        $panel = $this->makePanel($request);

        $this->get('/debug-kit/panels/view/' . $panel->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Request</h2>');
        $this->assertResponseContains('Attributes</h4>');
    }

    /**
     * Test getting a panel that does notexists.
     *
     * @return void
     */
    public function testViewNotExists(): void
    {
        $this->get('/debug-kit/panels/view/aaaaaaaa-ffff-ffff-ffff-aaaaaaaaaaaa');
        $this->assertResponseError();
        $this->assertResponseContains('Error page');
    }

    /**
     * Deprecations panel renders the "No deprecations" flash only when
     * every category is empty (including the `other` bucket, which was
     * previously missing from the check).
     *
     * @return void
     */
    public function testViewDeprecationsPanelEmpty()
    {
        $request = $this->makeRequest();
        $panel = $this->makePanel(
            $request,
            'DebugKit.Deprecations',
            'Deprecations',
            'DebugKit.deprecations_panel',
            ['app' => [], 'cake' => [], 'vendor' => [], 'plugins' => [], 'other' => []],
        );

        $this->get("/debug-kit/panels/view/{$panel->id}");

        $this->assertResponseOk();
        $this->assertResponseContains('No deprecations');
    }

    /**
     * @return void
     */
    public function testViewDeprecationsPanelWithEntries()
    {
        $request = $this->makeRequest();
        $entry = ['niceFile' => 'src/Foo.php', 'line' => 1, 'message' => 'deprecated thing'];
        $panel = $this->makePanel(
            $request,
            'DebugKit.Deprecations',
            'Deprecations',
            'DebugKit.deprecations_panel',
            ['app' => [$entry], 'cake' => [], 'vendor' => [], 'plugins' => [], 'other' => []],
        );

        $this->get("/debug-kit/panels/view/{$panel->id}");

        $this->assertResponseOk();
        $this->assertResponseNotContains('No deprecations');
        $this->assertResponseContains('deprecated thing');
    }

    /**
     * Deprecations only present in the `other` bucket should also suppress
     * the "No deprecations" flash. Guards against a regression of M3 where
     * `count($other)` was missing from the empty-check sum.
     *
     * @return void
     */
    public function testViewDeprecationsPanelOtherOnly()
    {
        $request = $this->makeRequest();
        $entry = ['niceFile' => 'src/Bar.php', 'line' => 2, 'message' => 'only-other deprecation'];
        $panel = $this->makePanel(
            $request,
            'DebugKit.Deprecations',
            'Deprecations',
            'DebugKit.deprecations_panel',
            ['app' => [], 'cake' => [], 'vendor' => [], 'plugins' => [], 'other' => [$entry]],
        );

        $this->get("/debug-kit/panels/view/{$panel->id}");

        $this->assertResponseOk();
        $this->assertResponseNotContains('No deprecations');
        $this->assertResponseContains('only-other deprecation');
    }

    /**
     * The Variables panel surfaces serialization-error messages from
     * `ToolbarService`; those messages can echo back attacker-controlled
     * data (e.g. via `__sleep` exceptions), so the template must escape
     * them. Guards against a regression of M4.
     *
     * @return void
     */
    public function testViewVariablesPanelErrorIsEscaped()
    {
        $request = $this->makeRequest();
        $panel = $this->makePanel(
            $request,
            'DebugKit.Variables',
            'Variables',
            'DebugKit.variables_panel',
            ['error' => '<script>alert(1)</script>', 'variables' => [], 'errors' => []],
        );

        $this->get("/debug-kit/panels/view/{$panel->id}");

        $this->assertResponseOk();
        $this->assertResponseNotContains('<script>alert(1)</script>');
        $this->assertResponseContains('&lt;script&gt;alert(1)&lt;/script&gt;');
    }

    /**
     * @return void
     */
    public function testLatestHistory(): void
    {
        $request = $this->fetchTable('DebugKit.Requests')->find('recent')->first();
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
