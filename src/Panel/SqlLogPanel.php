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
namespace DebugKit\Panel;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use DebugKit\Database\Log\DebugLog;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 */
class SqlLogPanel extends DebugPanel {

/**
 * Loggers connected
 *
 * @var array
 */
	protected $_loggers = [];

/**
 * Startup hook - configures logger.
 *
 * This will unfortunately build all the connections, but they
 * won't connect until used.
 *
 * @param \Cake\Event\Event $event The event.
 * @return array
 */
	public function initialize(Event $event) {
		$configs = ConnectionManager::configured();
		foreach ($configs as $name) {
			$connection = ConnectionManager::get($name);
			if ($connection->configName() === 'debug_kit') {
				continue;
			}
			$logger = null;
			if ($connection->logQueries()) {
				$logger = $connection->logger();
			}

			$spy = new DebugLog($logger, $name);
			$this->_loggers[] = $spy;
			$connection->logQueries(true);
			$connection->logger($spy);
		}
	}

/**
 * Stores the data this panel wants.
 *
 * @param \Cake\Event\Event $event The event.
 * @return array
 */
	public function shutdown(Event $event) {
		$this->_data = [
			'tables' => array_map(function($table) {
				return $table->alias();
			},  TableRegistry::genericInstances()),
			'loggers' => $this->_loggers,
		];
	}
}
