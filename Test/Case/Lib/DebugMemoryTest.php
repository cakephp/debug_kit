<?php
/**
 * DebugKit Debug Memory Test Cases
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

App::uses('DebugMemory', 'DebugKit.Lib');

/**
 * Class DebugMemoryTest
 */
class DebugMemoryTest extends CakeTestCase {

/**
 * test memory usage
 *
 * @return void
 */
	public function testMemoryUsage() {
		$result = DebugMemory::getCurrent();
		$this->assertTrue(is_int($result));

		$result = DebugMemory::getPeak();
		$this->assertTrue(is_int($result));
	}

/**
 * test making memory use markers.
 *
 * @return void
 */
	public function testMemorySettingAndGetting() {
		DebugMemory::clear();
		$result = DebugMemory::record('test marker');
		$this->assertTrue($result);

		$result = DebugMemory::getAll(true);
		$this->assertCount(1, $result);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(is_numeric($result['test marker']));

		$result = DebugMemory::getAll();
		$this->assertEmpty($result);

		DebugMemory::record('test marker');
		DebugMemory::record('test marker');
		$result = DebugMemory::getAll();

		$this->assertCount(2, $result);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(isset($result['test marker #2']));
	}
}
