<?php
/**
 * DebugKit TimedBehavior test case
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.models.behaviors
 * @since         DebugKit 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('DebugKitDebugger', 'DebugKit.Lib');

class TimedBehaviorTestCase extends CakeTestCase {

	public $fixtures = array('core.article');

/**
 * startTest callback
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Article = ClassRegistry::init('Article');
		$this->Article->Behaviors->attach('DebugKit.Timed');
	}

/**
 * end a test
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Article);
		ClassRegistry::flush();
		DebugKitDebugger::clearTimers();
	}

/**
 * test find timers
 *
 * @return void
 */
	public function testFindTimers() {
		$timers = DebugKitDebugger::getTimers(false);
		$this->assertEquals(count($timers), 1);

		$this->Article->find('all');
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEquals(count($result), 2);
		
		$this->Article->find('all');
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEquals(count($result), 3);
	}

/**
 * test save timers
 *
 * @return void
 */
	public function testSaveTimers() {
		$timers = DebugKitDebugger::getTimers(false);
		$this->assertEquals(count($timers), 1);

		$this->Article->save(array('user_id' => 1, 'title' => 'test', 'body' => 'test'));
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEquals(count($result), 2);
	}
}
