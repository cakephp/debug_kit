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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use DebugKit\Panel\RoutesPanel;

/**
 * Class RoutesPanelTest
 *
 */
class RoutesPanelTest extends TestCase
{
    /**
     * @var RoutesPanel
     */
    protected $panel;

    /**
     * set up
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Router::defaultRouteClass(DashedRoute::class);
        Router::scope('/', function (RouteBuilder $routes) {
            $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
            $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
            $routes->fallbacks(DashedRoute::class);
        });

        $this->panel = new RoutesPanel();
    }

    /**
     * Test data
     *
     * @return void
     */
    public function testData()
    {
        $this->panel->shutdown(new Event('Controller.shutdown'));

        $result = $this->panel->data();

        $this->assertArrayHasKey('matchedRoute', $result);
        $this->assertNull($result['matchedRoute']);

        $this->assertArrayHasKey('routes', $result);
        $this->assertEquals([
            '/',
            '/pages/*',
            '/:controller',
            '/:controller/:action/*',
        ], Hash::extract($result['routes'], '{n}.template'));

    }

    /**
     * Test that the log panel outputs a summary.
     *
     * @return void
     */
    public function testSummary()
    {
        $this->panel->initialize();

        $this->assertEquals(4, $this->panel->summary());

        Router::connect('/test', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $this->assertEquals(5, $this->panel->summary());
    }
}
