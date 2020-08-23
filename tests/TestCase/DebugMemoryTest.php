<?php
declare(strict_types=1);

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
 */
namespace DebugKit\Test\TestCase;

use Cake\TestSuite\TestCase;
use DebugKit\DebugMemory;

/**
 * Class DebugMemoryTest
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
        $this->assertIsInt($result);

        $result = DebugMemory::getPeak();
        $this->assertIsInt($result);
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
        $this->assertStringContainsString('DebugMemoryTest.php line ' . (__LINE__ - 3), array_keys($result)[0]);
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
        $this->assertIsNumeric($result['test marker']);

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
