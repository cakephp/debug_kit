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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\View\Helper;

use Cake\Core\App;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\Helper\FormHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use DebugKit\View\Helper\HtmlToolbarHelper;
use DebugKit\View\Helper\ToolbarHelper;
use StdClass;

/**
 * Class ToolbarHelperTestCase
 */
class ToolbarHelperTest extends TestCase
{

    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Router::connect('/:controller/:action');

        $request = new Request();
        $request->addParams(['controller' => 'pages', 'action' => 'display']);

        $this->View = new View($request);
        $this->Toolbar = new ToolbarHelper($this->View);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Toolbar);
    }

    /**
     * Test makeNeatArray with basic types.
     *
     * @return void
     */
    public function testMakeNeatArrayBasic()
    {
        $in = false;
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', '0', '/strong', '(false)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = null;
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', '0', '/strong', '(null)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = true;
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', '0', '/strong', '(true)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = [];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', '0', '/strong', '(empty)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test that cyclic references can be printed.
     *
     * @return void
     */
    public function testMakeNeatArrayCyclicObjects()
    {
        $a = new StdClass;
        $b = new StdClass;
        $a->child = $b;
        $b->parent = $a;

        $in = ['obj' => $a];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            ['ul' => ['class' => 'neat-array depth-0']],
            '<li', '<strong', 'obj', '/strong', '(object)',
            ['ul' => ['class' => 'neat-array depth-1']],
            '<li', '<strong', 'child', '/strong', '(object)',
            ['ul' => ['class' => 'neat-array depth-2']],
            '<li', '<strong', 'parent', '/strong',
            '(object) - recursion',
            '/li',
            '/ul',
            '/li',
            '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test that duplicate references can be printed.
     *
     * @return void
     */
    public function testMakeNeatArrayDuplicateObjects()
    {
        $a = new StdClass;
        $b = new StdClass;
        $a->first = $b;
        $a->second = $b;

        $in = ['obj' => $a];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            ['ul' => ['class' => 'neat-array depth-0']],
            '<li', '<strong', 'obj', '/strong', '(object)',
            ['ul' => ['class' => 'neat-array depth-1']],
            '<li', '<strong', 'first', '/strong', '(object)',
            ['ul' => ['class' => 'neat-array depth-2']],
            '/ul',
            '/li',
            '<li', '<strong', 'second', '/strong', '(object)',
            ['ul' => ['class' => 'neat-array depth-2']],
            '/ul',
            '/li',
            '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test Neat Array formatting
     *
     * @return void
     */
    public function testMakeNeatArray()
    {
        $in = ['key' => 'value'];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = ['key' => null];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', 'key', '/strong', '(null)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = ['key' => 'value', 'foo' => 'bar'];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'foo', '/strong', 'bar', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = [
            'key' => 'value',
            'foo' => [
                'this' => 'deep',
                'another' => 'value'
            ]
        ];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'foo', '/strong',
                '(array)',
                ['ul' => ['class' => 'neat-array depth-1']],
                '<li', '<strong', 'this', '/strong', 'deep', '/li',
                '<li', '<strong', 'another', '/strong', 'value', '/li',
                '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = [
            'key' => 'value',
            'foo' => [
                'this' => 'deep',
                'another' => 'value'
            ],
            'lotr' => [
                'gandalf' => 'wizard',
                'bilbo' => 'hobbit'
            ]
        ];
        $result = $this->Toolbar->makeNeatArray($in, 1);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0 expanded'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'foo', '/strong',
                '(array)',
                ['ul' => ['class' => 'neat-array depth-1']],
                '<li', '<strong', 'this', '/strong', 'deep', '/li',
                '<li', '<strong', 'another', '/strong', 'value', '/li',
                '/ul',
            '/li',
            '<li', '<strong', 'lotr', '/strong',
                '(array)',
                ['ul' => ['class' => 'neat-array depth-1']],
                '<li', '<strong', 'gandalf', '/strong', 'wizard', '/li',
                '<li', '<strong', 'bilbo', '/strong', 'hobbit', '/li',
                '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Toolbar->makeNeatArray($in, 2);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0 expanded'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'foo', '/strong',
                '(array)',
                ['ul' => ['class' => 'neat-array depth-1 expanded']],
                '<li', '<strong', 'this', '/strong', 'deep', '/li',
                '<li', '<strong', 'another', '/strong', 'value', '/li',
                '/ul',
            '/li',
            '<li', '<strong', 'lotr', '/strong',
                '(array)',
                ['ul' => ['class' => 'neat-array depth-1 expanded']],
                '<li', '<strong', 'gandalf', '/strong', 'wizard', '/li',
                '<li', '<strong', 'bilbo', '/strong', 'hobbit', '/li',
                '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);

        $in = ['key' => 'value', 'array' => []];
        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            'ul' => ['class' => 'neat-array depth-0'],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'array', '/strong', '(empty)', '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test makeNeatArray with object inputs.
     *
     * @return void
     */
    public function testMakeNeatArrayObjects()
    {
        $in = new StdClass();
        $in->key = 'value';
        $in->nested = new StdClass();
        $in->nested->name = 'mark';

        $result = $this->Toolbar->makeNeatArray($in);
        $expected = [
            ['ul' => ['class' => 'neat-array depth-0']],
            '<li', '<strong', 'key', '/strong', 'value', '/li',
            '<li', '<strong', 'nested', '/strong',
            '(object)',
            ['ul' => ['class' => 'neat-array depth-1']],
            '<li', '<strong', 'name', '/strong', 'mark', '/li',
            '/ul',
            '/li',
            '/ul'
        ];
        $this->assertHtml($expected, $result);
    }
}
