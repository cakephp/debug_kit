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
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\LogPanel;

/**
 * Class LogPanelTest
 *
 */
class LogPanelTest extends TestCase {

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
 * Teardown method.
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		Log::drop('debug_kit_log_panel');
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
 * test shutdown
 *
 * @return void
 */
	public function testShutdown() {
		$event = new Event('Sample');

		Log::write('error', 'Test');

		$this->panel->shutdown($event);
		$result = $this->panel->data();

		$this->assertArrayHasKey('logger', $result);
		$logger = $result['logger'];

		$this->assertInstanceOf('DebugKit\Log\Engine\DebugKitLog', $logger);
		$this->assertCount(1, $logger->all()['error']);
	}
}
