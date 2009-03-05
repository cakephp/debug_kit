<?php
/**
 * View Group Test for debugkit
 *
 * 
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright       Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link            http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package         debug_kit
 * @subpackage      debug_kit.tests.groups
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * AllCoreHelpersGroupTest class
 *
 * @package       cake
 * @subpackage    cake.tests.groups
 */
class DebugKitViewGroupTest extends GroupTest {
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
	function DebugKitViewGroupTest() {
		$testDir = dirname(dirname(__FILE__));
		TestManager::addTestCasesFromDirectory($this, $testDir . DS . 'cases' . DS . 'views');
	}
}
?>