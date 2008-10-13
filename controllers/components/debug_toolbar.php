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
 * @copyright		Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link			http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.
 * @since			CakePHP v 1.2.0.4487
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Requires its own debugger class for now.
 */
App::import('Vendor', 'DebugKit.DebugKitDebugger');

class DebugToolbarComponent extends Object {
	var $controller;
/**
 * Components used by DebugToolbar
 *
 * @var string
 */
	var $components = array('RequestHandler');
/**
 * the default panels the toolbar uses.
 * which panels are used can be configured when attaching the component
 *
 * @var array
 */
	var $_defaultPanels = array('session', 'timers', 'request', 'sqlLog', 'memory');
	
	var $panels = array();
/**
 * initialize
 *
 * If debug is off the component will be disabled and not do any further time tracking
 * or view switching.
 *
 * @return bool
 **/
	function initialize(&$controller, $settings) {
		if (Configure::read('debug') == 0) {
			$this->enabled = false;
			return false;
		}
		DebugKitDebugger::startTimer('componentInit', __('Component initialization and startup', true));
		if (!isset($settings['panels'])) {
			$settings['panels'] = $this->_defaultPanels;
		}
		$this->_loadPanels($settings['panels']);
		unset($settings['panels']);
		
		$this->_set($settings);
		$this->controller =& $controller;
		return false;
	}
	
/**
 * Component Startup
 *
 * @return bool
 **/
	function startup(&$controller) {
		DebugKitDebugger::stopTimer('componentInit');
		
		$panels = array_keys($this->panels);
		foreach ($panels as $panelName) {
			$this->panels[$panelName]->startup($controller);
		}
		
		DebugKitDebugger::startTimer('controllerAction', __('Controller Action start', true));
	}
/**
 * beforeRender callback
 *
 * @return void
 **/
	function beforeRender(&$controller) {
		DebugKitDebugger::stopTimer('controllerAction');
		
		DebugKitDebugger::startTimer('ControllerRender', __('Begin Rendering', true));
	}
	
/**
 * Load Panels used in the debug toolbar
 *
 * @return 	void
 * @access protected
 **/
	function _loadPanels($panels) {
		foreach ($panels as $panel) {
			$className = $panel . 'Panel';
			if (!class_exists($className) && !App::import('Vendor', 'debug_panels' . DS .$className)) {
				trigger_error(sprintf(__('Could not load Panel %s', true), $panel), E_USER_WARNING);
				continue;
			}
			$panelObj =& new $className();
			if (is_subclass_of($panelObj, 'DebugPanel') || is_subclass_of($panelObj, 'DebugPanel')) {
				$this->panels[$panel] =& $panelObj;
			}
		}
	}
}

/**
 * Debug Panel
 *
 * Abstract class for debug panels.
 *
 * @package cake.debug_kit
 */
class DebugPanel extends Object {
/**
 * startup the panel
 *
 * Pull information from the controller / request
 *
 * @param object $controller Controller reference.
 * @return void
 **/
	function startup(&$controller) { }

/**
 * Prepare output vars before Controller Rendering.
 *
 * @param object $controller Controller reference.
 * @return void
 **/
	function beforeRender(&$controller) { }

}


/**
 * Session Panel
 *
 * Provides debug information on the Session contents.
 *
 * @package cake.debug_kit.panels
 **/
class SessionPanel extends DebugPanel {
	
}

/**
 * Request Panel
 *
 * Provides debug information on the Current request params.
 *
 * @package cake.debug_kit.panels
 **/
class RequestPanel extends DebugPanel {
	
}

?>