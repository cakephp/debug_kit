<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase;

use Cake\TestSuite\TestCase;
use DebugKit\DebugMemory;

/**
 * Class DebugMemoryTest
 *
 */
class DebugMemoryTest extends TestCase
{

    /**
     * test memory usage
     *
     * @return void
     */
    public function testMemoryUsage()
    {
        $result = DebugMemory::getCurrent();
        $this->assertTrue(is_int($result));

        $result = DebugMemory::getPeak();
        $this->assertTrue(is_int($result));
    }

    /**
     * Test record() automatic naming
     *
     * @return void
     */
    public function testRecordNoKey()
    {
        DebugMemory::clear();
        DebugMemory::record();
        $result = DebugMemory::getAll(true);
        $this->assertCount(1, $result);
        $this->assertContains('DebugMemoryTest.php line ' . (__LINE__ - 3), array_keys($result)[0]);
    }

    /**
     * test making memory use markers.
     *
     * @return void
     */
    public function testMemorySettingAndGetting()
    {
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
