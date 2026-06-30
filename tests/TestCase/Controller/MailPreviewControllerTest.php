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

/**
 * Mail preview controller test
 */
class MailPreviewControllerTest extends TestCase
{
    use FixtureFactoryTrait;
    use IntegrationTestTrait;

    /**
     * Test that plugin is passed to the view in email action
     *
     * @return void
     */
    public function testEmailPluginPassedToView(): void
    {
        $this->get('/debug-kit/mail-preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseOk();
        $this->assertResponseContains('src="?part=html&plugin=DebugkitTestPlugin');
    }

    /**
     * Test that invalid classnames are rejected
     *
     * @return void
     */
    public function testEmailRejectInvalidClassName()
    {
        $this->get('/debug-kit/mail-preview/preview/Cake\Utility\Inflector/slug');
        $this->assertResponseCode(404);

        $this->get('/debug-kit/mail-preview/preview/Invalid/hello');
        $this->assertResponseCode(404);
    }

    /** Test email template content
     *
     * @return void
     */
    public function testEmailPartTextContent(): void
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
    public function testOnChangeJsPluginPassedToview(): void
    {
        $this->get('/debug-kit/mail-preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseContains("iframe.contentWindow.location.replace('?part=' + part_name + '&plugin=DebugkitTestPlugin');");
    }

    /**
     * Test sent() with invalid data.
     *
     * @return void
     */
    public function testSentInvalidData(): void
    {
        $this->get('/debug-kit/mail-preview/sent/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa/0');
        $this->assertResponseCode(404);
    }

    /**
     * Test sent() with valid data.
     *
     * @return void
     */
    public function testSentValidData(): void
    {
        $panels = $this->fetchTable('DebugKit.Panels');
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

        $this->get(sprintf('/debug-kit/mail-preview/sent/%s/0', $panel->id));
        $this->assertResponseCode(200);
        $this->assertResponseContains('test@example.com');
        $this->assertResponseContains('<iframe');
    }

    /**
     * Test sent() with valid data rendering a part
     *
     * @return void
     */
    public function testSentValidDataRenderPart(): void
    {
        $panels = $this->fetchTable('DebugKit.Panels');
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

        $this->get(sprintf('/debug-kit/mail-preview/sent/%s/0?part=html', $panel->id));
        $this->assertResponseCode(200);
        $this->assertResponseContains('<h1>Hi</h1>');
    }
}
