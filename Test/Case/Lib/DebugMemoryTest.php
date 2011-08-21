<?php
App::uses('DebugMemory', 'DebugKit.Lib');

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
		$this->assertEqual(count($result), 1);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(is_numeric($result['test marker']));

		$result = DebugMemory::getAll();
		$this->assertTrue(empty($result));

		DebugMemory::record('test marker');
		DebugMemory::record('test marker');
		$result = DebugMemory::getAll();

		$this->assertEqual(count($result), 2);
		$this->assertTrue(isset($result['test marker']));
		$this->assertTrue(isset($result['test marker #2']));
	}
}
