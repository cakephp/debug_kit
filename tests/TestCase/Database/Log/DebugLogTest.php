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
namespace DebugKit\Test\TestCase\Database\Log;

use Cake\Database\Log\LoggedQuery;
use Cake\TestSuite\TestCase;
use DebugKit\Database\Log\DebugLog;

/**
 * DebugLog test case
 */
class DebugLogTest extends TestCase
{

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->logger = new DebugLog(null, 'test');
    }

    /**
     * Test logs being stored.
     *
     * @return void
     */
    public function testLog()
    {
        $query = new LoggedQuery();
        $query->query = 'SELECT * FROM posts';
        $query->took = 10;
        $query->numRows = 5;

        $this->assertCount(0, $this->logger->queries());

        $this->logger->log($query);
        $this->assertCount(1, $this->logger->queries());
        $this->assertEquals(10, $this->logger->totalTime());
        $this->assertEquals(5, $this->logger->totalRows());

        $this->logger->log($query);
        $this->assertCount(2, $this->logger->queries());
        $this->assertEquals(20, $this->logger->totalTime());
        $this->assertEquals(10, $this->logger->totalRows());
    }

    /**
     * Test decoration of logger.
     *
     * @return void
     */
    public function testLogDecorates()
    {
        $orig = $this->getMock('Cake\Database\Log\QueryLogger');
        $orig->expects($this->once())
            ->method('log');

        $query = new LoggedQuery();
        $logger = new DebugLog($orig, 'test');
        $logger->log($query);
    }

    /**
     * Test logs contain extra information if queryString is present.
     *
     * @return void
     */
    public function testLoggedQueryFormatWithExtra()
    {
        $query = new LoggedQuery();
        $query->query = 'SELECT * FROM posts WHERE id = :id';
        $query->params = ['id' => 1];
        $query->took = 10;
        $query->numRows = 1;

        $this->logger->log($query);

        $query = new LoggedQuery();
        $query->query = $query->queryString = 'SELECT * FROM posts WHERE id = :id';
        $query->params = ['id' => 2];
        $query->took = 10;
        $query->numRows = 1;

        $this->logger->log($query);

        $expected = [
            [
                'query' => 'SELECT * FROM posts WHERE id = 1',
                'took' => 10,
                'rows' => 1,
            ],
            [
                'query' => 'SELECT * FROM posts WHERE id = 2',
                'took' => 10,
                'rows' => 1,
                'queryString' => 'SELECT * FROM posts WHERE id = :id',
                'params' => ['id' => 2],
            ],
        ];

        $this->assertEquals($expected, $this->logger->queries());
    }
}
