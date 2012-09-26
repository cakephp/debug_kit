<?php
/**
 * DebugKit ToolbarAccess Model
 *
 * Contains logic for accessing DebugKit specific information.
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.controllers
 * @since         DebugKit 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::uses('ConnectionManager', 'Model');

class ToolbarAccess extends Object {

/**
 * Runs an explain on a query if the connection supports EXPLAIN.
 * currently only PostgreSQL and MySQL are supported.
 *
 * @param string $connection Connection name
 * @param string $query SQL query to explain / find query plan for.
 * @return array Array of explain information or empty array if connection is unsupported.
 */
	public function explainQuery($connection, $query) {
		$db = ConnectionManager::getDataSource($connection);
		$datasource = $db->config['datasource'];

		$return = array();
		if (preg_match('/(Mysql|Postgres)$/', $datasource)) {
			$explained = $db->query('EXPLAIN ' . $query);
			if (preg_match('/Postgres$/', $datasource)) {
				$queryPlan = array();
				foreach ($explained as $postgreValue) {
					$queryPlan[] = array($postgreValue[0]['QUERY PLAN']);
				}
				$return = array_merge(array(array('')), $queryPlan);
			} else {
				$keys = array_keys($explained[0][0]);
				foreach ($explained as $mysqlValue) {
					$queryPlan[] = array_values($mysqlValue[0]);
				}
				$return = array_merge(array($keys), $queryPlan);
			}
		}
		return $return;
	}
}