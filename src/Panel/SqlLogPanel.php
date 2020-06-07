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
namespace DebugKit\Panel;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use DebugKit\Database\Log\DebugLog;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 */
class SqlLogPanel extends DebugPanel
{
    use LocatorAwareTrait;

    /**
     * Loggers connected
     *
     * @var array
     */
    protected $_loggers = [];

    /**
     * Initialize hook - configures logger.
     *
     * This will unfortunately build all the connections, but they
     * won't connect until used.
     *
     * @return void
     */
    public function initialize()
    {
        $configs = ConnectionManager::configured();
        $includeSchemaReflection = (bool)Configure::read('DebugKit.includeSchemaReflection');

        foreach ($configs as $name) {
            $connection = ConnectionManager::get($name);
            if (
                $connection->configName() === 'debug_kit'
                || !$connection instanceof ConnectionInterface
            ) {
                continue;
            }
            $logger = null;
            if ($connection->isQueryLoggingEnabled()) {
                $logger = $connection->getLogger();
            }

            if ($logger instanceof DebugLog) {
                $logger->setIncludeSchema($includeSchemaReflection);
                $this->_loggers[] = $logger;
                continue;
            }
            $logger = new DebugLog($logger, $name, $includeSchemaReflection);

            $connection->enableQueryLogging(true);
            $connection->setLogger($logger);

            $this->_loggers[] = $logger;
        }
    }

    /**
     * Get the data this panel wants to store.
     *
     * @return array
     */
    public function data()
    {
        return [
            'tables' => array_map(function (Table $table) {
                return $table->getAlias();
            }, $this->getTableLocator()->genericInstances()),
            'loggers' => $this->_loggers,
        ];
    }

    /**
     * Get summary data from the queries run.
     *
     * @return string
     */
    public function summary()
    {
        $count = $time = 0;
        foreach ($this->_loggers as $logger) {
            $count += count($logger->queries());
            $time += $logger->totalTime();
        }
        if (!$count) {
            return '0';
        }

        return "$count / $time ms";
    }
}
