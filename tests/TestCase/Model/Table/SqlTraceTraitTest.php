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
 * @since         5.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;

/**
 * Tests for SqlTraceTrait debugging comments.
 */
class SqlTraceTraitTest extends TestCase
{
    use LocatorAwareTrait;

    /**
     * Fixtures
     */
    public array $fixtures = [
        'plugin.DebugKit.Requests',
        'plugin.DebugKit.Panels',
    ];

    /**
     * Table names.
     */
    public array $tables = [
        'DebugKit.Panels',
        'DebugKit.Requests',
    ];

    protected bool $debug;

    protected function setUp(): void
    {
        parent::setUp();
        $this->debug = Configure::read('App.debug', true);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::write('App.debug', $this->debug);
    }

    /**
     * Verify file name when calling find()
     */
    public function testFind()
    {
        foreach ($this->tables as $table) {
            $table = $this->fetchTable($table);
            $sql = $table->find()->select(['id'])->sql();
            $this->assertTrue(str_contains($sql, basename(__FILE__)), 'Expected file: ' . $sql);
        }
    }

    /**
     * Verify file name when calling query()/select()
     */
    public function testQuery()
    {
        foreach ($this->tables as $table) {
            $table = $this->fetchTable($table);
            $sql = $table->query()->sql();
            $this->assertTrue(str_contains($sql, basename(__FILE__)), 'Expected file: ' . $sql);
        }
    }

    /**
     * Verify file name when calling update()
     */
    public function testUpdate()
    {
        foreach ($this->tables as $table) {
            $table = $this->fetchTable($table);
            $sql = $table->updateQuery()->set(['title' => 'fooBar'])->sql();
            $this->assertTrue(str_contains($sql, basename(__FILE__)), 'Expected file: ' . $sql);
        }
    }

    /**
     * Verify file name when calling delete()
     */
    public function testDelete()
    {
        foreach ($this->tables as $table) {
            $table = $this->fetchTable($table);
            $sql = $table->deleteQuery()->where(['title' => 'fooBar'])->sql();
            $this->assertTrue(str_contains($sql, basename(__FILE__)), 'Expected file: ' . $sql);
        }
    }
}
