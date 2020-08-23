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

use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use DebugKit\DebugSql;

/**
 * Test the debugging SQL
 */
class DebugSqlTest extends TestCase
{
    /**
     * @var \Cake\Database\Connection
     */
    public $connection;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->connection = ConnectionManager::get('test');
    }

    /**
     * Tests that a SQL string is outputted as text on the CLI.
     */
    public function testSqlText()
    {
        $query = $this->newQuery()->select(['panels.id']);

        ob_start();
        $this->assertSame($query, DebugSql::sql($query));
        $result = ob_get_clean();

        $expectedText = <<<EXPECTED
%s (line %d)
########## DEBUG ##########
SELECT panels.id AS %s FROM panels panels
###########################

EXPECTED;
        $fieldName = $this->connection->getDriver() instanceof Postgres ? '"panels__id"' : 'panels__id';
        $expected = sprintf($expectedText, str_replace(ROOT, '', __FILE__), __LINE__ - 11, $fieldName);
        $this->assertSame($expected, $result);
    }

    /**
     * Tests that a SQL string is outputted as HTML.
     */
    public function testSqlHtml()
    {
        $query = $this->newQuery()->select(['panels.id']);

        ob_start();
        $this->assertSame($query, DebugSql::sql($query, true, true));
        $result = ob_get_clean();

        $expectedHtml = <<<EXPECTED
<div class="cake-debug-output">
<span><strong>%s</strong> (line <strong>%d</strong>)</span>
<pre class="cake-debug">
SELECT 
  panels.id AS %s 
FROM 
  panels panels
</pre>
</div>
EXPECTED;
        $fieldName = $this->connection->getDriver() instanceof Postgres ? '"panels__id"' : 'panels__id';
        $expected = sprintf($expectedHtml, str_replace(ROOT, '', __FILE__), __LINE__ - 15, $fieldName);
        $this->assertSame(str_replace("\r", '', $expected), str_replace("\r", '', $result));
    }

    /**
     * Creates a Query object for testing.
     *
     * @return Query
     */
    private function newQuery()
    {
        return $this->getTableLocator()->get('panels')->query();
    }
}
