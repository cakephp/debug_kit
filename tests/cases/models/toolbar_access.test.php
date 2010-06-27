<?php
/**
 * DebugKit ToolbarAccess Model Test case
 *
 * PHP versions 4 and 5
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
App::import('Model', 'DebugKit.ToolbarAccess');
/**
 * Test case for ToolbarAccess model
 *
 * @package debug_kit
 */
class ToolbarAccessTestCase extends CakeTestCase {
/**
 * Included fixtures
 *
 * @var array
 */
	var $fixtures = array('core.post');

/**
 * startTest method
 *
 * @return void
 */
	function startTest() {
		$this->Model =& new ToolbarAccess();
	}

/**
 * endTest
 *
 * @return void
 */
	function endTest() {
		unset($this->Model);
	}

/**
 * test that explain query returns arrays of query information.
 *
 * @return void
 */
	function testExplainQuery() {
		$db =& ConnectionManager::getDataSource('test_suite');
		$sql = 'SELECT * FROM ' . $db->fullTableName('posts') . ';';
		$result = $this->Model->explainQuery('test_suite', $sql);

		$this->assertTrue(is_array($result));
		$this->assertFalse(empty($result));
	}
}
