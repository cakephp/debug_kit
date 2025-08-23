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
namespace DebugKit\Panel;

use Cake\Core\Configure;
use Cake\Database\Driver;
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
    protected static array $_loggers = [];

    /**
     * Initialize hook - configures logger.
     *
     * This will unfortunately build all the connections, but they
     * won't connect until used.
     *
     * @return void
     */
    public function initialize(): void
    {
        $configs = ConnectionManager::configured();

        foreach ($configs as $name) {
            static::addConnection($name);
        }
    }

    /**
     * Add a connection to the list of loggers.
     *
     * @param string $name The name of the connection to add.
     * @return void
     */
    public static function addConnection(string $name): void
    {
        $includeSchemaReflection = (bool)Configure::read('DebugKit.includeSchemaReflection');

        $connection = ConnectionManager::get($name);
        if ($connection->configName() === 'debug_kit') {
            return;
        }
        $driver = $connection->getDriver();

        if (!method_exists($driver, 'setLogger')) {
            return;
        }

        $logger = null;
        if ($driver instanceof Driver) {
            $logger = $driver->getLogger();
        } elseif (method_exists($connection, 'getLogger')) {
            // ElasticSearch connection holds the logger, not the Elastica Driver
            $logger = $connection->getLogger();
        }

        if ($logger instanceof DebugLog) {
            $logger->setIncludeSchema($includeSchemaReflection);
            static::$_loggers[] = $logger;

            return;
        }
        $logger = new DebugLog($logger, $name, $includeSchemaReflection);

        /** @var \Cake\Database\Driver $driver */
        $driver->setLogger($logger);

        static::$_loggers[] = $logger;
    }

    /**
     * Get the data this panel wants to store.
     *
     * @return array
     */
    public function data(): array
    {
        return [
            'tables' => array_map(function (Table $table) {
                return $table->getAlias();
            }, $this->getTableLocator()->genericInstances()),
            'loggers' => static::$_loggers,
        ];
    }

    /**
     * Get summary data from the queries run.
     *
     * @return string
     */
    public function summary(): string
    {
        $count = $time = 0;
        foreach (static::$_loggers as $logger) {
            $count += count($logger->queries());
            $time += $logger->totalTime();
        }
        if (!$count) {
            return '0';
        }

        return "$count / $time ms";
    }
}
