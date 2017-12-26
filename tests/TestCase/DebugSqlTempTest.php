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
 */
namespace DebugKit\Test\TestCase;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\DebugSqlTemp;

/**
 * Test the debugging SQL
 */
class DebugSqlTestTemp extends TestCase
{
    /**
     * @var \Cake\Database\Connection
     */
    public $connection;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.debug_kit.panels'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        $this->connection = ConnectionManager::get('test');
    }

    /**
     * Test that a file name is found when a closure is used.
     */
    public function testFileStampClosure()
    {
        $query = $this->newQuery()->select(['id']);
        $func = function ($query) {
            $this->assertSame($query, DebugSqlTemp::fileStamp($query));

            return $query;
        };
        $query = $func($query);
        $comment = sprintf(str_replace(DS, '\\', '/* ROOT\tests\TestCase\DebugSqlTempTest.php (line %d) */'), __LINE__ - 5);
        $sql = (string)$query;
        $this->assertTrue(strpos($sql, $comment) !== false, 'Expected: ' . $comment . ' Found: ' . $sql);
    }

    /**
     * No comment should be set when debug is off.
     * @skip
     */
    public function testFileStampDebugOff()
    {
        $query = $this->newQuery()->select(['id']);
        $debug = Configure::read('debug');
        Configure::write('debug', false);
        $sql = (string)$query;
        $this->assertSame($query, DebugSqlTemp::fileStamp($query, 1, true));
        $this->assertEquals($sql, (string)$query);
        Configure::write('debug', $debug);
    }

    /**
     * Verify file name is correct when the table object calls the query() method on itself.
     */
    public function testFileStampFileName()
    {
        $query = $this->newQuery()->select(['id']);
        $this->assertSame($query, DebugSqlTemp::fileStamp($query));
        $comment = sprintf(str_replace(DS, '\\', '/* ROOT\tests\TestCase\DebugSqlTempTest.php (line %d) */'), __LINE__ - 1);
        $sql = (string)$query;
        $this->assertTrue(strpos($sql, $comment) !== false, 'Expected: ' . $comment . ' Found: ' . $sql);
    }

    /**
     * Creates a Query object for testing.
     *
     * @return Query
     */
    private function newQuery()
    {
        return new Query($this->connection, TableRegistry::get('panels'));
    }
}
