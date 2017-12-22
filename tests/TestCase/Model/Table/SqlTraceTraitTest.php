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
 * @since         3.11.5
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Tests for SqlTraceTrait debugging comments.
 */
class SqlTraceTraitTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.debug_kit.panels',
        'plugin.debug_kit.requests'
    ];

    /**
     * Table names.
     *
     * @var array
     */
    public $tables = [
        'debug_kit.panels',
        'debug_kit.requests'
    ];

    /**
     * Verify file name when calling find()
     */
    public function testFind()
    {
        foreach ($this->tables as $table) {
            $table = TableRegistry::get($table);
            $sql = (string)$table->find()->select(['id']);
            $this->assertTrue(strpos($sql, basename(__FILE__)) !== false, 'Expected file: ' . $sql);
        }
    }

    /**
     * Verify file name when calling query()
     */
    public function testQuery()
    {
        foreach ($this->tables as $table) {
            $table = TableRegistry::get($table);
            $sql = (string)$table->query();
            $this->assertTrue(strpos($sql, basename(__FILE__)) !== false, 'Expected file: ' . $sql);
        }
    }

    /**
     * Verify file name when calling update()
     */
    public function testUpdate()
    {
        foreach ($this->tables as $table) {
            $table = TableRegistry::get($table);
            $sql = (string)$table->query()->update()->set(['title' => 'fooBar']);
            $this->assertTrue(strpos($sql, basename(__FILE__)) !== false, 'Expected file: ' . $sql);
        }
    }
}
