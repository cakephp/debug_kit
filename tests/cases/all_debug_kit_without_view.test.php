<?php

require_once App::pluginPath('DebugKit') . 'tests' . DS . 'lib' . DS . 'debug_kit_group_test.php';

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
 * @subpackage    cake.tests.cases
 */

class AllDebugKitWithoutViewTest extends DebugkitGroupTest {
/**
 *
 *
 * @access public
 * @return void
 */
	public static function suite() {
		$suite = self::_getSuite();
		$suite->addTestFiles(self::_testFiles(null, 'views'));

		return $suite;
	}
}