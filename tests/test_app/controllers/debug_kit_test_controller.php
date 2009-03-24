<?php
/* SVN FILE: $Id$ */
/**
 * DebugKitTestController
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
 * @subpackage      tests.test_app.controllers
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class DebugKitTestController extends Controller {
	var $uses = array();
	var $components = array('DebugKit.Toolbar');

	function request_action_return() {
		$this->autoRender = false;
		return 'I am some value from requestAction.';
	}
	
	function request_action_render() {
		$this->set('test', 'I have been rendered.');
	}
	
}