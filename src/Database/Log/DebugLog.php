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
 * @since         3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Database\Log;

use Cake\Database\Log\LoggedQuery;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * DebugKit Query logger.
 *
 * This logger decorates the existing logger if it exists,
 * and stores log messages internally so they can be displayed
 * or stored for future use.
 */
class DebugLog extends AbstractLogger
{
    /**
     * Logs from the current request.
     *
     * @var array
     */
    protected $_queries = [];

    /**
     * Decorated logger.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $_logger;

    /**
     * Name of the connection being logged.
     *
     * @var string
     */
    protected $_connectionName;

    /**
     * Total time (ms) of all queries
     *
     * @var int
     */
    protected $_totalTime = 0;

    /**
     * Total rows of all queries
     *
     * @var int
     */
    protected $_totalRows = 0;

    /**
     * Set to true to capture schema reflection queries
     * in the SQL log panel.
     *
     * @var bool
     */
    protected $_includeSchema = false;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface|null $logger The logger to decorate and spy on.
     * @param string $name The name of the connection being logged.
     * @param bool $includeSchema Whether or not schema reflection should be included.
     */
    public function __construct(?LoggerInterface $logger, string $name, bool $includeSchema = false)
    {
        $this->_logger = $logger;
        $this->_connectionName = $name;
        $this->_includeSchema = $includeSchema;
    }

    /**
     * Set the schema include flag.
     *
     * @param bool $value Set
     * @return $this
     */
    public function setIncludeSchema(bool $value)
    {
        $this->_includeSchema = $value;

        return $this;
    }

    /**
     * Get the connection name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->_connectionName;
    }

    /**
     * Get the stored logs.
     *
     * @return array
     */
    public function queries(): array
    {
        return $this->_queries;
    }

    /**
     * Get the total time
     *
     * @return int
     */
    public function totalTime(): int
    {
        return $this->_totalTime;
    }

    /**
     * Get the total rows
     *
     * @return int
     */
    public function totalRows(): int
    {
        return $this->_totalRows;
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        $query = $context['query'];

        if ($this->_logger) {
            $this->_logger->log($level, $message, $context);
        }

        if ($this->_includeSchema === false && $this->isSchemaQuery($query)) {
            return;
        }

        $this->_totalTime += $query->took;
        $this->_totalRows += $query->numRows;

        $this->_queries[] = [
            'query' => (string)$query,
            'took' => $query->took,
            'rows' => $query->numRows,
        ];
    }

    /**
     * Sniff SQL statements for things only found in schema reflection.
     *
     * @param \Cake\Database\Log\LoggedQuery $query The query to check.
     * @return bool
     */
    protected function isSchemaQuery(LoggedQuery $query): bool
    {
        $querystring = $query->query;

        return // Multiple engines
            strpos($querystring, 'FROM information_schema') !== false ||
            // Postgres
            strpos($querystring, 'FROM pg_catalog') !== false ||
            // MySQL
            strpos($querystring, 'SHOW TABLE') === 0 ||
            strpos($querystring, 'SHOW FULL COLUMNS') === 0 ||
            strpos($querystring, 'SHOW INDEXES') === 0 ||
            // Sqlite
            strpos($querystring, 'FROM sqlite_master') !== false ||
            strpos($querystring, 'PRAGMA') === 0 ||
            // Sqlserver
            strpos($querystring, 'FROM INFORMATION_SCHEMA') !== false ||
            strpos($querystring, 'FROM sys.') !== false;
    }
}
