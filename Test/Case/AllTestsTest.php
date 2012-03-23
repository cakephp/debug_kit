<?php

require_once dirname(__FILE__) . DS . 'DebugkitGroupTestCase.php';

/**
 * AllTestsTest For DebugKit
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
 * @subpackage    debug_kit.tests.groups
 * @since         DebugKit 1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
/**
 * AllTestsTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */

class AllTestsTest extends DebugkitGroupTestCase {
/**
 *
 * @return PHPUnit_Framework_TestSuite the instance of PHPUnit_Framework_TestSuite
 */
	public static function suite() {
		$suite = new self;
		$files = $suite->getTestFiles();
		$suite->addTestFiles($files);

		return $suite;
	}
}
