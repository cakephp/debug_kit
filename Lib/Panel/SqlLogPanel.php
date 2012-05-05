<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * SqlLog Panel
 *
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 * @package       cake.debug_kit.panels
 */
class SqlLogPanel extends DebugPanel {

/**
 * Minimum number of Rows Per Millisecond that must be returned by a query before an explain
 * is done.
 *
 * @var int
 */
	public $slowRate = 20;

/**
 * Gets the connection names that should have logs + dumps generated.
 *
 * @param string $controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		if (!class_exists('ConnectionManager')) {
			return array();
		}
		$connections = array();

		$dbConfigs = ConnectionManager::sourceList();
		foreach ($dbConfigs as $configName) {
			$driver = null;
			$db = ConnectionManager::getDataSource($configName);
			if (
				(empty($db->config['driver']) && empty($db->config['datasource'])) ||
				!method_exists($db, 'getLog')
			) {
				continue;
			}
			if (isset($db->config['datasource'])) {
				$driver = $db->config['datasource'];
			}
			$explain = false;
			$isExplainable = (preg_match('/(Mysql|Postgres)$/', $driver));
			if ($isExplainable) {
				$explain = true;
			}
			$connections[$configName] = $explain;
		}
		return array('connections' => $connections, 'threshold' => $this->slowRate);
	}
}
