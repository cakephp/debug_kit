<?php
/**
 * Common test objects used in DebugKit tests
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
 * @subpackage    debug_kit.tests
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
/**
 * TestFireCake class allows for testing of FireCake
 *
 * @package debug_kit.tests.
 */
class TestFireCake extends FireCake {
	var $sentHeaders = array();

	function _sendHeader($name, $value) {
		$_this =& FireCake::getInstance();
		$_this->sentHeaders[$name] = $value;
	}
/**
 * skip client detection as headers are not being sent.
 *
 * @access public
 * @return void
 */	
	function detectClientExtension() {
		return true;
	}
/**
 * Reset the fireCake
 *
 * @return void
 **/
	function reset() {
		$_this =& FireCake::getInstance();
		$_this->sentHeaders = array();
		$_this->_messageIndex = 1;
	}
}


