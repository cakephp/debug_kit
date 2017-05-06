<?php
namespace DebugKit\Test\Mailer;

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use DebugKit\Mailer\PreviewResult;
use DebugkitTestPlugin\Mailer\Preview\TestMailerPreview;

class PreviewResultTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Router::scope('/', function (RouteBuilder $routes) {
            $routes->fallbacks(DashedRoute::class);
        });

        Router::plugin('DebugKitTest', function (RouteBuilder $routes) {
            $routes->connect(
                '/Example',
                ['controller' => 'Example', 'action' => 'index']
            );
        });
        Plugin::routes();
    }

    public function tearDown()
    {
        Router::reload();
        parent::tearDown();
    }

    public function testPreviewResultBodyContainAppArrayUrl()
    {
        $mailPreview = new TestMailerPreview();
        $result = new PreviewResult(
            $mailPreview->contain_app_array_url(),
            'contain_app_array_url'
        );
        $resultParts = $result->getParts();
        $this->assertContains('http://localhost/users/verified', $resultParts['text']);
    }

    public function testPreviewResultBodyContainPluginArrayUrl()
    {
        $mailPreview = new TestMailerPreview();
        $result = new PreviewResult(
            $mailPreview->contain_plugin_array_url(),
            'contain_plugin_array_url'
        );
        $resultParts = $result->getParts();
        $this->assertContains('http://localhost/debug_kit_test/Example', $resultParts['text']);
    }
}
