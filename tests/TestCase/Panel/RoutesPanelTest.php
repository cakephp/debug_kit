<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
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

        Router::defaultRouteClass('DashedRoute');
        Router::scope('/', function (RouteBuilder $routes) {
            $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
            $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
            $routes->fallbacks('DashedRoute');
        });

        // Force Router::$initialized to be true.
        Router::url(['controller' => 'Pages', 'action' => 'display', 'contact']);

        $this->panel = new RoutesPanel();
    }

    /**
     * Test that the log panel outputs a summary.
     *
     * @return void
     */
    public function testSummary()
    {
        $this->panel->initialize();
        $this->assertSame('4', $this->panel->summary());

        Router::connect('/test', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $this->assertSame('5', $this->panel->summary());
    }
}
