<?php
/* SVN FILE: $Id$ */
/**
 * DebugKit Debugger Test Case File
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake.tests
 * @subpackage    cake.tests.cases.libs
 * @since         CakePHP(tm) v 1.2.0.5432
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('Core', 'Debugger');
App::import('Vendor', 'DebugKit.DebugKitDebugger');

require_once APP . 'plugins' . DS . 'debug_kit' . DS . 'tests' . DS . 'cases' . DS . 'test_objects.php';

/**
 * Short description for class.
 *
 * @package       cake.tests
 * @subpackage    cake.tests.cases.libs
 */
class DebugKitDebuggerTest extends CakeTestCase {
/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		Configure::write('log', false);
		if (!defined('SIMPLETESTVENDORPATH')) {
			if (file_exists(APP . DS . 'vendors' . DS . 'simpletest' . DS . 'reporter.php')) {
				define('SIMPLETESTVENDORPATH', 'APP' . DS . 'vendors');
			} else {
				define('SIMPLETESTVENDORPATH', 'CORE' . DS . 'vendors');
			}
		}
	}

/**
 * Start Timer test
 *
 * @return void
 **/
	function testTimers() {
		$this->assertTrue(DebugKitDebugger::startTimer('test1', 'this is my first test'));
		usleep(5000);
		$this->assertTrue(DebugKitDebugger::stopTimer('test1'));
		$elapsed = DebugKitDebugger::elapsedTime('test1');
		$this->assertTrue($elapsed > 0.0050);

		$this->assertTrue(DebugKitDebugger::startTimer('test2', 'this is my second test'));
		sleep(1);
		$this->assertTrue(DebugKitDebugger::stopTimer('test2'));
		$elapsed = DebugKitDebugger::elapsedTime('test2');
		$this->assertTrue($elapsed > 1);

		DebugKitDebugger::startTimer('test3');
		$this->assertFalse(DebugKitDebugger::elapsedTime('test3'));
		$this->assertFalse(DebugKitDebugger::stopTimer('wrong'));
	}

/**
 * testRequestTime
 *
 * @access public
 * @return void
 */
	function testRequestTime() {
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
	function testGetTimers() {
		DebugKitDebugger::clearTimers();
		DebugKitDebugger::startTimer('test1', 'this is my first test');
		DebugKitDebugger::stopTimer('test1');
		usleep(50);
		DebugKitDebugger::startTimer('test2');
		DebugKitDebugger::stopTimer('test2');
		$timers = DebugKitDebugger::getTimers();

		$this->assertEqual(count($timers), 2);
		$this->assertTrue(is_float($timers['test1']['time']));
		$this->assertTrue(isset($timers['test1']['message']));
		$this->assertTrue(isset($timers['test2']['message']));
	}
	
/**
 * test memory usage
 *
 * @return void
 **/
	function testMemoryUsage() {
		$result = DebugKitDebugger::getMemoryUse();
		$this->assertTrue(is_int($result));
		
		$result = DebugKitDebugger::getPeakMemoryUse();
		$this->assertTrue(is_int($result));
	}
/**
 * test _output switch to firePHP
 *
 * @return void
 */
	function testOutput() {
		$firecake =& FireCake::getInstance('TestFireCake');
		Debugger::invoke(DebugKitDebugger::getInstance('DebugKitDebugger'));
		Debugger::output('fb');
		$foo .= '';
		$result = $firecake->sentHeaders;
		
		$this->assertPattern('/GROUP_START/', $result['X-Wf-1-1-1-1']);
		$this->assertPattern('/ERROR/', $result['X-Wf-1-1-1-2']);
		$this->assertPattern('/GROUP_END/', $result['X-Wf-1-1-1-5']);
		
		Debugger::invoke(Debugger::getInstance('Debugger'));
		Debugger::output();
	}

/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		Configure::write('log', true);
	}

}
?>