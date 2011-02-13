<?php
/**
 * DebugKit Debugger Test Case File
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
 * @subpackage    debug_kit.tests.vendors
 * @since         debug_kit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Vendor', 'DebugKit.DebugKitDebugger');
require_once App::pluginPath('DebugKit') . 'tests' . DS . 'cases' . DS . 'test_objects.php';

/**
 * Test case for the DebugKitDebugger
 *
 * @package       debug_kit.tests
 * @subpackage    debug_kit.tests.cases.vendors
 */
class DebugKitDebuggerTest extends CakeTestCase {
/**
 * setUp method
 *
 * @access public
 * @return void
 */
	public function setUp() {
		Configure::write('log', false);
	}
/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	public function tearDown() {
		Configure::write('log', true);
		DebugKitDebugger::clearTimers();
	}
/**
 * Start Timer test
 *
 * @return void
 **/
	public function testTimers() {
		$this->assertTrue(DebugKitDebugger::startTimer('test1', 'this is my first test'));
		usleep(5000);
		$this->assertTrue(DebugKitDebugger::stopTimer('test1'));
		$elapsed = DebugKitDebugger::elapsedTime('test1');
		$this->assertTrue($elapsed > 0.0050);

		$this->assertTrue(DebugKitDebugger::startTimer('test2', 'this is my second test'));
		sleep(1);
		$this->assertTrue(DebugKitDebugger::stopTimer('test2'));
		$elapsed = DebugKitDebugger::elapsedTime('test2');
		$expected = strpos(PHP_OS, 'WIN') === false ? 1 : 0.95; // Windows timer's precision is bad
		$this->assertTrue($elapsed > $expected);

		DebugKitDebugger::startTimer('test3');
		$this->assertIdentical(DebugKitDebugger::elapsedTime('test3'), 0);
		$this->assertFalse(DebugKitDebugger::stopTimer('wrong'));
	}
/**
 * test timers with no names.
 *
 * @return void
 **/
	public function testAnonymousTimers() {
		$this->assertTrue(DebugKitDebugger::startTimer());
		usleep(2000);
		$this->assertTrue(DebugKitDebugger::stopTimer());
		$timers = DebugKitDebugger::getTimers();

		$this->assertEqual(count($timers), 2);
		end($timers);
		$key = key($timers);
		$lineNo = __LINE__ - 8;

		$file = Debugger::trimPath(__FILE__);
		$expected = $file . ' line ' . $lineNo;
		$this->assertEqual($key, $expected);

		$timer = $timers[$expected];
		$this->assertTrue($timer['time'] > 0.0020);
		$this->assertEqual($timers[$expected]['message'], $expected);
	}
/**
 * Assert that nested anonymous timers don't get mixed up.
 *
 * @return void
 **/
	public function testNestedAnonymousTimers() {
		$this->assertTrue(DebugKitDebugger::startTimer());
		usleep(100);
		$this->assertTrue(DebugKitDebugger::startTimer());
		usleep(100);
		$this->assertTrue(DebugKitDebugger::stopTimer());
		$this->assertTrue(DebugKitDebugger::stopTimer());

		$timers = DebugKitDebugger::getTimers();
		$this->assertEqual(count($timers), 3, 'incorrect number of timers %s');
		$firstTimerLine = __LINE__ -9;
		$secondTimerLine = __LINE__ -8;
		$file = Debugger::trimPath(__FILE__);

		$this->assertTrue(isset($timers[$file . ' line ' . $firstTimerLine]), 'first timer is not set %s');
		$this->assertTrue(isset($timers[$file . ' line ' . $secondTimerLine]), 'second timer is not set %s');

		$firstTimer = $timers[$file . ' line ' . $firstTimerLine];
		$secondTimer = $timers[$file . ' line ' . $secondTimerLine];
		$this->assertTrue($firstTimer['time'] > $secondTimer['time']);
	}
/**
 * test that calling startTimer with the same name does not overwrite previous timers
 * and instead adds new ones.
 *
 * @return void
 **/
	public function testRepeatTimers() {
		DebugKitDebugger::startTimer('my timer', 'This is the first call');
		usleep(100);
		DebugKitDebugger::startTimer('my timer', 'This is the second call');
		usleep(100);

		DebugKitDebugger::stopTimer('my timer');
		DebugKitDebugger::stopTimer('my timer');

		$timers = DebugKitDebugger::getTimers();
		$this->assertEqual(count($timers), 3, 'wrong timer count %s');

		$this->assertTrue(isset($timers['my timer']));
		$this->assertTrue(isset($timers['my timer #2']));

		$this->assertTrue($timers['my timer']['time'] > $timers['my timer #2']['time'], 'timer 2 is longer? %s');
		$this->assertEqual($timers['my timer']['message'], 'This is the first call');
		$this->assertEqual($timers['my timer #2']['message'], 'This is the second call #2');
	}
/**
 * testRequestTime
 *
 * @access public
 * @return void
 */
	public function testRequestTime() {
		$result1 = DebugKitDebugger::requestTime();
		usleep(50);
		$result2 = DebugKitDebugger::requestTime();
		$this->assertTrue($result1 < $result2);
	}
/**
 * test getting all the set timers.
 *
 * @return void
 **/
	public function testGetTimers() {
		DebugKitDebugger::startTimer('test1', 'this is my first test');
		DebugKitDebugger::stopTimer('test1');
		usleep(50);
		DebugKitDebugger::startTimer('test2');
		DebugKitDebugger::stopTimer('test2');
		$timers = DebugKitDebugger::getTimers();

		$this->assertEqual(count($timers), 3);
		$this->assertTrue(is_float($timers['test1']['time']));
		$this->assertTrue(isset($timers['test1']['message']));
		$this->assertTrue(isset($timers['test2']['message']));
	}
/**
 * test memory usage
 *
 * @return void
 **/
	public function testMemoryUsage() {
		$result = DebugKitDebugger::getMemoryUse();
		$this->assertTrue(is_int($result));

		$result = DebugKitDebugger::getPeakMemoryUse();
		$this->assertTrue(is_int($result));
	}
/**
 * test output switch to firePHP
 *
 * @return void
 */
	public function testOutput() {

		$firecake = FireCake::getInstance('TestFireCake');
		Debugger::getInstance('DebugKitDebugger');
		Debugger::output('fb');

		set_error_handler('ErrorHandler::handleError');
		$foo .= '';
		restore_error_handler();

		$result = $firecake->sentHeaders;

		$this->assertPattern('/GROUP_START/', $result['X-Wf-1-1-1-1']);
		$this->assertPattern('/ERROR/', $result['X-Wf-1-1-1-2']);
		$this->assertPattern('/GROUP_END/', $result['X-Wf-1-1-1-5']);

		Debugger::getInstance('Debugger');
		Debugger::output();
	}
/**
 * test making memory use markers.
 *
 * @return void
 **/
	public function testMemorySettingAndGetting() {
		DebugKitDebugger::clearMemoryPoints();
		$result = DebugKitDebugger::setMemoryPoint('test marker');
		$this->assertTrue($result);

		$result = DebugKitDebugger::getMemoryPoints(true);
		$this->assertEqual(count($result), 1);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(is_numeric($result['test marker']));

		$result = DebugKitDebugger::getMemoryPoints();
		$this->assertTrue(empty($result));

		DebugKitDebugger::setMemoryPoint('test marker');
		DebugKitDebugger::setMemoryPoint('test marker');
		$result = DebugKitDebugger::getMemoryPoints();
		$this->assertEqual(count($result), 2);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(isset($result['test marker #2']));
	}

}
