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
 * @since         3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Database\Log;

use Cake\Database\Log\LoggedQuery;
use Cake\Database\Log\QueryLogger;
use Psr\Log\AbstractLogger as PsrAbstractLogger;

/**
 * DebugKit Query logger.
 *
 * This logger decorates the existing logger if it exists,
 * and stores log messages internally so they can be displayed
 * or stored for future use.
 */
class DebugLog extends QueryLogger
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
     * @var \Cake\Database\Log\LoggedQuery
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
     * @param \Cake\Database\Log\QueryLogger $logger The logger to decorate and spy on.
     * @param string $name The name of the connection being logged.
     * @param bool $includeSchema Whether or not schema reflection should be included.
     */
    public function __construct($logger, $name, $includeSchema = false)
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
    public function setIncludeSchema($value)
    {
        $this->_includeSchema = $value;

        return $this;
    }

    /**
     * Get the connection name.
     *
     * @return array
     */
    public function name()
    {
        return $this->_connectionName;
    }

    /**
     * Get the stored logs.
     *
     * @return array
     */
    public function queries()
    {
        return $this->_queries;
    }

    /**
     * Get the total time
     *
     * @return int
     */
    public function totalTime()
    {
        return $this->_totalTime;
    }

    /**
     * Get the total rows
     *
     * @return int
     */
    public function totalRows()
    {
        return $this->_totalRows;
    }

    /**
     * Log queries
     *
     * @param \Cake\Database\Log\LoggedQuery $query The query being logged.
     * @return void
     */
    public function log(LoggedQuery $query)
    {
        if ($this->_logger) {
            if ($this->_logger instanceof PsrAbstractLogger) {
                $this->_logger->log($query, $query->error);
            } else {
                $this->_logger->log($query);
            }
        }

        if ($this->_includeSchema === false && $this->isSchemaQuery($query)) {
            return;
        }

        if (!empty($query->params)) {
            $query->query = $this->_interpolate($query);
        }
        $this->_totalTime += $query->took;
        $this->_totalRows += $query->numRows;

        $this->_queries[] = [
            'query' => $query->query,
            'took' => $query->took,
            'rows' => $query->numRows
        ];
    }

    /**
     * Sniff SQL statements for things only found in schema reflection.
     *
     * @param \Cake\Database\Log\LoggedQuery $query The query to check.
     * @return bool
     */
    protected function isSchemaQuery(LoggedQuery $query)
    {
        $querystring = $query->query;

        return (
            // Multiple engines
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
            strpos($querystring, 'FROM sys.') !== false
        );
    }
}
