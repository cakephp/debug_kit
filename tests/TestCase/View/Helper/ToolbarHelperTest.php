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

use Cake\Error\Debug\TextFormatter;
use Cake\Error\Debugger;
use Cake\Http\ServerRequest as Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DebugKit\View\Helper\ToolbarHelper;
use SimpleXmlElement;

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
        $path = '//*[@class="cake-dbg-array-item"]/*[@class="cake-dbg-string"]';
        $data = ['z' => 1, 'a' => 99, 'm' => 123];
        $nodes = array_map(function ($v) {
            return Debugger::exportVarAsNodes($v);
        }, $data);
        $result = $this->Toolbar->dumpNodes($nodes);
        $xml = new SimpleXmlElement($result);
        $elements = $xml->xpath($path);
        $this->assertSame(["'z'", "'a'", "'m'"], array_map('strval', $elements));

        $this->Toolbar->setSort(true);
        $result = $this->Toolbar->dumpNodes($nodes);
        $xml = new SimpleXmlElement($result);
        $elements = $xml->xpath($path);
        $this->assertSame(["'a'", "'m'", "'z'"], array_map('strval', $elements));
    }

    public function testDumpCoerceHtml()
    {
        $restore = Debugger::configInstance('exportFormatter');
        Debugger::configInstance('exportFormatter', TextFormatter::class);
        $result = $this->Toolbar->dump(false);
        $this->assertMatchesRegularExpression('/<\w/', $result, 'Contains HTML tags.');
        $this->assertSame(
            TextFormatter::class,
            Debugger::configInstance('exportFormatter'),
            'Should restore setting'
        );

        // Restore back to original value.
        Debugger::configInstance('exportFormatter', $restore);
    }

    public function testDumpSorted()
    {
        $path = '//*[@class="cake-dbg-array-item"]/*[@class="cake-dbg-string"]';
        $data = ['z' => 1, 'a' => 99, 'm' => 123];
        $result = $this->Toolbar->dump($data);
        $xml = new SimpleXmlElement($result);
        $elements = $xml->xpath($path);
        $this->assertSame(["'z'", "'a'", "'m'"], array_map('strval', $elements));

        $this->Toolbar->setSort(true);
        $result = $this->Toolbar->dump($data);
        $xml = new SimpleXmlElement($result);
        $elements = $xml->xpath($path);
        $this->assertSame(["'a'", "'m'", "'z'"], array_map('strval', $elements));
    }
}
