<?php
/**
 * View Group Test for debugkit
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
 * @subpackage    debug_kit.tests.groups
 * @since         DebugKit 1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
/**
 * DebugKitViewTestSuite class
 *
 * @package       cake
 * @subpackage    cake.tests.groups
 */
class DebugKitViewTestSuite extends TestSuite {
/**
 * label property
 *
 * @var string 'All core helpers'
 * @access public
 */
	var $label = 'All View layer tests for DebugKit';
/**
 * AllCoreHelpersGroupTest method
 *
 * @access public
 * @return void
 */
	function DebugKitViewTestSuite() {
		$testDir = dirname(dirname(__FILE__));
		TestManager::addTestCasesFromDirectory($this, $testDir . DS . 'cases' . DS . 'views');
	}
}
