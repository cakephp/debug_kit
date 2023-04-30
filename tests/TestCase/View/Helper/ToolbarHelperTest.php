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
 * @since         DebugKit 0.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\View\Helper;

use Cake\Error\Debugger;
use Cake\Http\ServerRequest as Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DebugKit\View\Helper\ToolbarHelper;
use DOMDocument;
use DOMXPath;

/**
 * Class ToolbarHelperTestCase
 */
class ToolbarHelperTest extends TestCase
{
    /**
     * @var View
     */
    protected $View;

    /**
     * @var ToolbarHelper
     */
    protected $Toolbar;

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
        $this->Toolbar = new ToolbarHelper($this->View);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Toolbar);
    }

    public function testDumpNodesSorted()
    {
        $path = '//*[@class="cake-debug-array-item"]/*[@class="cake-debug-string"]';
        $data = ['z' => 1, 'a' => 99, 'm' => 123];
        $nodes = array_map(function ($v) {
            return Debugger::exportVarAsNodes($v);
        }, $data);
        $result = $this->Toolbar->dumpNodes($nodes);
        $doc = new DOMDocument();
        $doc->loadHTML($result);
        $elements = new DOMXPath($doc);

        $result = [];
        foreach ($elements->query($path) as $elem) {
            $result[] = $elem->nodeValue;
        }
        $expected = ["'z'", "'a'", "'m'"];
        $this->assertSame($expected, $result);

        $this->Toolbar->setSort(true);
        $result = $this->Toolbar->dumpNodes($nodes);
        $doc = new DOMDocument();
        $doc->loadHTML($result);
        $elements = new DOMXPath($doc);

        $result = [];
        foreach ($elements->query($path) as $elem) {
            $result[] = $elem->nodeValue;
        }
        $expected = ["'a'", "'m'", "'z'"];
        $this->assertSame($expected, $result);
    }
}
