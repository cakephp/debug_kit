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
namespace Cake\DebugKit\Panel;

use Cake\Controller\Controller;
use Cake\DebugKit\DebugPanel;
use Cake\DebugKit\Database\Log\DebugLog;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

/**
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 */
class SqlLogPanel extends DebugPanel {

/**
 * Minimum number of Rows Per Millisecond that must be returned by a query before an explain
 * is done.
 *
 * @var integer
 */
	public $slowRate = 20;

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
	public function startup(Event $event) {
		$configs = ConnectionManager::configured();
		foreach ($configs as $name) {
			$connection = ConnectionManager::get($name);
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
 * Gets the connection names that should have logs + dumps generated.
 *
 * @param \Cake\Event\Event $event The event.
 * @return array
 */
	public function beforeRender(Event $event) {
		return [
			'loggers' => $this->_loggers,
			'threshold' => $this->slowRate,
		];
	}
}
