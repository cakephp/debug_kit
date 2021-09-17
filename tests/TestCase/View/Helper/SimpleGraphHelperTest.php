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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest as Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DebugKit\View\Helper\SimpleGraphHelper;

/**
 * Class SimpleGraphHelperTestCase
 */
class SimpleGraphHelperTest extends TestCase
{
    /**
     * @var View
     */
    protected $View;

    /**
     * @var SimpleGraphHelper
     */
    protected $Graph;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Router::createRouteBuilder('/')->connect('/{controller}/{action}');

        $request = new Request();
        $request = $request->withParam('controller', 'pages')->withParam('action', 'display');

        $this->View = new View($request);
        $this->Graph = new SimpleGraphHelper($this->View);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Graph);
    }

    /**
     * Test bar()
     *
     * @return void
     */
    public function testBar()
    {
        $output = $this->Graph->bar(10, 0);
        $expected = [
            ['div' => [
                'class' => 'graph-bar',
                'style' => 'width: 350px',
            ]],
            ['div' => [
                'class' => 'graph-bar-value',
                'style' => 'margin-left: 0px; width: 35px',
                'title' => 'Starting 0ms into the request, taking 10ms',
            ]],
            ' ',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $output);
    }

    /**
     * Test bar() with offset
     *
     * @return void
     */
    public function testBarOffset()
    {
        $output = $this->Graph->bar(10, 10);
        $expected = [
            ['div' => [
                'class' => 'graph-bar',
                'style' => 'width: 350px',
            ]],
            ['div' => [
                'class' => 'graph-bar-value',
                'style' => 'margin-left: 35px; width: 35px',
                'title' => 'Starting 10ms into the request, taking 10ms',
            ]],
            ' ',
            '/div',
            '/div',
        ];
        $this->assertHtml($expected, $output);
    }
}
