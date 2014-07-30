<?php
/**
 * DebugKit Log Panel Test Cases
 *
 * PHP 5
 *
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

use Cake\Controller\Controller;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use DebugKit\Lib\Panel\LogPanel;

/**
 * Class LogPanelTest
 *
 */
class LogPanelTest extends CakeTestCase {

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->panel = new LogPanel();
	}

/**
 * Test that logging configs are created.
 *
 * @return void
 */
	public function testConstructor() {
		$result = Log::configured();
		$this->assertContains('debug_kit_log_panel', $result);
		$this->assertTrue(count($result) > 1, 'Default loggers were not added.');
	}

/**
 * testBeforeRender
 *
 * @return void
 */
	public function testBeforeRender() {
		$controller = new Controller();

		Log::write('error', 'Test');

		$result = $this->panel->beforeRender($controller);
		$this->assertInstanceOf('DebugKitLog', $result);
		$this->assertTrue(isset($result->logs));
		$this->assertCount(1, $result->logs['error']);
	}
}
