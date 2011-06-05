<?php
/**
 * DebugKit TimedBehavior test case
 *
 * PHP versions 4 and 5
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
App::import('Vendor', 'DebugKit.DebugKitDebugger');

class TimedBehaviorTestCase extends CakeTestCase {

	var $fixtures = array('core.article');
/**
 * startTest callback
 *
 * @return void
 */
	function startTest() {
		$this->Article =& new Model(array('ds' => 'test_suite', 'table' => 'articles', 'name' => 'Article'));
		$this->Article->Behaviors->attach('DebugKit.Timed');
	}

/**
 * end a test
 *
 * @return void
 */
	function endTest() {
		unset($this->Article);
		ClassRegistry::flush();
		DebugKitDebugger::clearTimers();
	}

/**
 * test find timers
 *
 * @return void
 */
	function testFindTimers() {
		$timers = DebugKitDebugger::getTimers(false);
		$this->assertEqual(count($timers), 1);

		$this->Article->find('all');
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEqual(count($result), 2);
		
		$this->Article->find('all');
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEqual(count($result), 3);
	}

/**
 * test save timers
 *
 * @return void
 */
	function testSaveTimers() {
		$timers = DebugKitDebugger::getTimers(false);
		$this->assertEqual(count($timers), 1);

		$this->Article->save(array('user_id' => 1, 'title' => 'test', 'body' => 'test'));
		$result = DebugKitDebugger::getTimers(false);
		$this->assertEqual(count($result), 2);
	}
}