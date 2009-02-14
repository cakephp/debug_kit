<?php
/* SVN FILE: $Id$ */
/**
 * DebugKit DebugToolbar Component
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
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       debug_kit
 * @subpackage    debug_kit.controllers.components
 * @since         DebugKit 0.1
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ToolbarAccessController extends DebugKitAppController {
/**
 * name
 *
 * @var string
 */
	var $name = 'ToolbarAccess';
/**
 * Uses
 *
 * @var array
 **/
	var $uses = array();
/**
 * components
 *
 * @var array
 **/
	var $components = array('RequestHandler');
/**
 * beforeFilter callback
 *
 * @return void
 **/
	function beforeFilter() {
		if (isset($this->Toolbar)) {
			$this->Toolbar->enabled = false;
		}
	}
/**
 * Get a stored history state from the toolbar cache.
 *
 * @return void
 **/
	function history_state($key = null) {
		$oldState = $this->Toolbar->loadState($key);
		$this->set('toolbarState', $oldState);
	}
}