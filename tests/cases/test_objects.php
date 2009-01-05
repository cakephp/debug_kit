<?php
/* SVN FILE: $Id$ */
/**
 * Common test objects used in DebugKit tests
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake.tests
 * @subpackage    cake.tests.cases.libs
 * @since         CakePHP(tm) v 1.2.0.5432
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * TestFireCake class allows for testing of FireCake
 *
 * @package debug_kit.tests.
 */
class TestFireCake extends FireCake {
	var $sentHeaders = array();

	function _sendHeader($name, $value) {
		$_this = FireCake::getInstance();
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
		$_this = FireCake::getInstance();
		$_this->sentHeaders = array();
		$_this->_messageIndex = 1;
	}
}

?>
