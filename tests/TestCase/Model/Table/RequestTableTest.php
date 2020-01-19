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
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Tests for request table.
 */
class RequestTableTest extends TestCase
{
    /**
     * Setup
     *
     * Skip tests on SQLite as SQLite complains when tables are changed while a connection is open.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $connection = ConnectionManager::get('test');
        $this->skipIf($connection->getDriver() instanceof Sqlite, 'Schema insertion/removal breaks SQLite');
    }

    /**
     * test that schema is created on-demand.
     *
     * @return void
     */
    public function testInitializeCreatesSchema()
    {
        $connection = ConnectionManager::get('test');
        $stmt = $connection->execute('DROP TABLE IF EXISTS panels');
        $stmt->closeCursor();

        $stmt = $connection->execute('DROP TABLE IF EXISTS requests');
        $stmt->closeCursor();

        TableRegistry::get('DebugKit.Requests');
        TableRegistry::get('DebugKit.Panels');

        $schema = $connection->getSchemaCollection();
        $this->assertContains('requests', $schema->listTables());
        $this->assertContains('panels', $schema->listTables());
    }

    /**
     * Test the recent finder.
     *
     * @return void
     */
    public function testFindRecent()
    {
        $table = TableRegistry::get('DebugKit.Requests');
        $query = $table->find('recent');
        $this->assertSame(10, $query->clause('limit'));
        $this->assertNotEmpty($query->clause('order'));
    }

    /**
     * Test the garbage collect.
     *
     * @return void
     */
    public function testGc()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\DebugKit\Model\Table\RequestsTable $requestsTableMock */
        $requestsTableMock = $this->getMockForModel('DebugKit.Requests', ['shouldGc']);
        $requestsTableMock->method('shouldGc')
            ->will($this->returnValue(true));

        $data = array_fill(0, 10, [
            'url' => '/tasks/add',
            'content_type' => 'text/html',
            'status_code' => 200,
            'requested_at' => '2014-08-21 7:41:12',
        ]);
        $requests = $requestsTableMock->newEntities($data);
        $this->assertNotFalse($requestsTableMock->saveMany($requests));

        $count = $requestsTableMock->find()->count();
        $this->assertGreaterThanOrEqual(10, $count);

        Configure::write('DebugKit.requestCount', 5);
        $requestsTableMock->gc();

        $count = $requestsTableMock->find()->count();
        $this->assertSame(5, $count);
    }
}
