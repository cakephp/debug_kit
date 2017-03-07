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

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Mail preview controller test
 */
class MailPreviewControllerTest extends IntegrationTestCase
{

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Plugin::load('DebugkitTestPlugin', ['path' => APP . 'Plugin' . DS . 'DebugkitTestPlugin' . DS]);
        Router::plugin('DebugKit', function (RouteBuilder $routes) {
            $routes->scope(
                '/mail_preview',
                ['controller' => 'MailPreview'],
                function ($routes) {
                    $routes->connect('/preview/*', ['action' => 'email']);
                }
            );
        });
    }
    /**
     * Test that plugin is passed to the view in email action
     *
     * @return void
     */
    public function testEmailPluginPassedToView()
    {
        $this->get('/debug_kit/mail_preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseOk();
        $this->assertResponseContains('src="?part=text&plugin=DebugkitTestPlugin');
    }
    /**
     * Test that onChange js function passes plugin to iframe
     *
     * @return void
     */
    public function testOnChangeJsPluginPassedToview()
    {
        $this->get('/debug_kit/mail_preview/preview/TestMailerPreview/test_email?plugin=DebugkitTestPlugin');

        $this->assertResponseContains("iframe.contentWindow.location.replace('?part=' + part_name + '&plugin=DebugkitTestPlugin');");
    }
}
