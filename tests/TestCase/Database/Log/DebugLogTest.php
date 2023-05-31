<?php
declare(strict_types=1);

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
namespace DebugKit\Test\TestCase\Database\Log;

use Cake\Database\Log\LoggedQuery;
use Cake\TestSuite\TestCase;
use DebugKit\Database\Log\DebugLog;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * DebugLog test case
 */
class DebugLogTest extends TestCase
{
    /**
     * @var DebugLog
     */
    protected $logger;

    /**
     * setup
     *
     * @return void
     */
    public function setUp(): void
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
        $query->setContext([
            'query' => 'SELECT * FROM posts',
            'took' => 10,
            'numRows' => 5,
        ]);

        $this->assertCount(0, $this->logger->queries());

        $this->logger->log(LogLevel::DEBUG, (string)$query, ['query' => $query]);
        $this->assertCount(1, $this->logger->queries());
        $this->assertSame(10.0, $this->logger->totalTime());

        $this->logger->log(LogLevel::DEBUG, (string)$query, ['query' => $query]);
        $this->assertCount(2, $this->logger->queries());
        $this->assertSame(20.0, $this->logger->totalTime());
    }

    /**
     * Test log ignores schema reflection
     *
     * @dataProvider schemaQueryProvider
     * @return void
     */
    public function testLogIgnoreReflection($sql)
    {
        $query = new LoggedQuery();
        $query->setContext([
            'query' => $sql,
            'took' => 10,
            'numRows' => 5,
        ]);

        $this->assertCount(0, $this->logger->queries());

        $this->logger->log(LogLevel::DEBUG, (string)$query, ['query' => $query]);
        $this->assertCount(0, $this->logger->queries());
    }

    /**
     * Test config setting turns off schema ignores
     *
     * @dataProvider schemaQueryProvider
     * @return void
     */
    public function testLogIgnoreReflectionDisabled($sql)
    {
        $query = new LoggedQuery();
        $query->setContext([
            'query' => $sql,
            'took' => 10,
            'numRows' => 5,
        ]);

        $logger = new DebugLog(null, 'test', true);
        $this->assertCount(0, $logger->queries());

        $logger->log(LogLevel::DEBUG, (string)$query, ['query' => $query]);
        $this->assertCount(1, $logger->queries());
    }

    public static function schemaQueryProvider()
    {
        return [
            // MySQL
            ['SHOW TABLES FROM database'],
            ['SHOW FULL COLUMNS FROM database.articles'],
            // general
            ['SELECT * FROM information_schema'],
            // sqlserver
            ['SELECT I.[name] FROM sys.[tables]'],
            ['SELECT [name] FROM sys.foreign_keys'],
            ['SELECT [name] FROM INFORMATION_SCHEMA.TABLES'],
            // sqlite
            ['PRAGMA index_info()'],
        ];
    }

    /**
     * Test decoration of logger.
     *
     * @return void
     */
    public function testLogDecorates()
    {
        $orig = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $orig->expects($this->once())
            ->method('log');

        $query = new LoggedQuery();
        $logger = new DebugLog($orig, 'test');
        $logger->log(LogLevel::DEBUG, (string)$query, ['query' => $query]);
    }
}
