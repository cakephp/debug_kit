<?php
App::uses('LogPanel', 'DebugKit.Lib/Panel');
App::uses('Controller', 'Controller');

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
		$result = CakeLog::configured();
		$this->assertContains('debug_kit_log_panel', $result);
		$this->assertContains('default', $result);
	}

	public function testBeforeRender() {
		$controller = new Controller();

		CakeLog::write('error', 'Test');

		$result = $this->panel->beforeRender($controller);
		$this->assertInstanceOf('DebugKitLogListener', $result);
		$this->assertTrue(isset($result->logs));
		$this->assertCount(1, $result->logs['error']);
	}

}
