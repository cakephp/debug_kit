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
use Cake\TestSuite\TestCase;
use DebugKit\Panel\DeprecationsPanel;

/**
 * Class DeprecationsPanelTest
 *
 */
class DeprecationsPanelTest extends TestCase
{
    /**
     * @var DeprecationsPanel
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
        DeprecationsPanel::clearDeprecatedErrors();

        $this->panel = new DeprecationsPanel();

        $previousHandler = set_error_handler(function ($code, $message, $file, $line, $context = null) {
            DeprecationsPanel::addDeprecatedError(compact('code', 'message', 'file', 'line', 'context'));
        });
        deprecationWarning('Something going away', 0);
        deprecationWarning('Something else going away', 0);
        trigger_error('Raw error', E_USER_DEPRECATED);

        set_error_handler($previousHandler);
    }

    public function testShutdown()
    {
        $event = new Event('Panel.shutdown');
        $this->panel->shutdown($event);
        $data = $this->panel->data();

        $this->assertArrayHasKey('app', $data);
        $this->assertArrayHasKey('cake', $data);
        $this->assertArrayHasKey('vendor', $data);
        $this->assertArrayHasKey('plugins', $data);
        $this->assertArrayHasKey('other', $data);
        $this->assertCount(3, $data['plugins']['DebugKit']);

        $error = $data['plugins']['DebugKit'][0];
        $this->assertContains('Something going away', $error['message']);
        $this->assertEquals('DebugKit/tests/TestCase/Panel/DeprecationsPanelTest.php', $error['niceFile']);
        $this->assertEquals(45, $error['line']);

        $error = $data['plugins']['DebugKit'][2];
        $this->assertContains('Raw error', $error['message']);
        $this->assertEquals('DebugKit/tests/TestCase/Panel/DeprecationsPanelTest.php', $error['niceFile']);
        $this->assertEquals(47, $error['line']);
    }

    public function testSummary()
    {
        $this->assertEquals('3', $this->panel->summary());
    }
}
