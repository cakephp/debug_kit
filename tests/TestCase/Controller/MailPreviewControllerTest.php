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

use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DebugKit\Test\TestCase\FixtureFactoryTrait;
use DebugKit\TestApp\Application;

/**
 * Mail preview controller test
 */
class MailPreviewControllerTest extends TestCase
{
    use FixtureFactoryTrait;
    use IntegrationTestTrait;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Router::createRouteBuilder('/')->connect('/users/{action}/*', ['controller' => 'Users']);
        $this->configApplication(Application::class, []);
    }

    /**
     * Test that plugin is passed to the view in email action
     *
     * @return void
     */
    public function testEmailPluginPassedToView()
    {
        $this->get('/debug-kit/mail-preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseOk();
        $this->assertResponseContains('src="?part=html&plugin=DebugkitTestPlugin');
    }

    /** Test email template content
     *
     * @return void
     */
    public function testEmailPartTextContent()
    {
        $this->get('/debug-kit/mail-preview/preview/TestMailerPreview/test_email?part=text&plugin=DebugkitTestPlugin');

        $this->assertResponseOk();
        $this->assertResponseContains('Testing email action.');
        $this->assertResponseContains('/users/verify/token', 'Should contain URL from app context');
    }

    /**
     * Test that onChange js function passes plugin to iframe
     *
     * @return void
     */
    public function testOnChangeJsPluginPassedToview()
    {
        $this->get('/debug-kit/mail-preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseContains("iframe.contentWindow.location.replace('?part=' + part_name + '&plugin=DebugkitTestPlugin');");
    }

    /**
     * Test sent() with invalid data.
     *
     * @return void
     */
    public function testSentInvalidData()
    {
        $this->get('/debug-kit/mail-preview/sent/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa/0');
        $this->assertResponseCode(404);
    }

    /**
     * Test sent() with valid data.
     *
     * @return void
     */
    public function testSentValidData()
    {
        $panels = $this->getTableLocator()->get('DebugKit.Panels');
        $request = $this->makeRequest();
        $panel = $panels->newEntity(['request_id' => $request->id]);
        $data = [
            'emails' => [
                [
                    'headers' => ['To' => 'test@example.com'],
                    'message' => ['html' => '<h1>Hi</h1>', 'text' => 'Hi'],
                ],
            ],
        ];
        $panel->content = serialize($data);
        $panels->save($panel);

        $this->get("/debug-kit/mail-preview/sent/{$panel->id}/0");
        $this->assertResponseCode(200);
        $this->assertResponseContains('test@example.com');
        $this->assertResponseContains('<iframe');
    }

    /**
     * Test sent() with valid data rendering a part
     *
     * @return void
     */
    public function testSentValidDataRenderPart()
    {
        $panels = $this->getTableLocator()->get('DebugKit.Panels');
        $request = $this->makeRequest();
        $panel = $panels->newEntity(['request_id' => $request->id]);
        $data = [
            'emails' => [
                [
                    'headers' => ['To' => 'test@example.com'],
                    'message' => ['html' => '<h1>Hi</h1>', 'text' => 'Hi'],
                ],
            ],
        ];
        $panel->content = serialize($data);
        $panels->save($panel);

        $this->get("/debug-kit/mail-preview/sent/{$panel->id}/0?part=html");
        $this->assertResponseCode(200);
        $this->assertResponseContains('<h1>Hi</h1>');
    }
}
