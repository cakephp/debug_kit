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
/**
 * Controller instance reference
 *
 * @var object
 */
	var $controller;
/**
 * Components used by DebugToolbar
 *
 * @var array
 */
	var $components = array('RequestHandler');
/**
 * the default panels the toolbar uses.
 * which panels are used can be configured when attaching the component
 *
 * @var array
 */
	var $_defaultPanels = array('session', 'request', 'sqlLog', 'memory', 'timer');
/**
 * Built panels
 *
 * @var array
 */	
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
		if (!isset($controller->params['url']['ext']) 
			|| (isset($controller->param['url']['ext']) 
			&& $controller->params['url']['ext'] == 'html')
		) {
			$controller->view = 'DebugKit.Debug';
		} else {
			//use firephp view class.
		}
		$panels = array_keys($this->panels);
		foreach ($panels as $panelName) {
			$this->panels[$panelName]->startup($controller);
		}
		
		DebugKitDebugger::stopTimer('componentInit');
		DebugKitDebugger::startTimer('controllerAction', __('Controller Action', true));
	}
/**
 * beforeRender callback
 *
 * Calls beforeRender on all the panels and set the aggregate to the controller.
 *
 * @return void
 **/
	function beforeRender(&$controller) {
		DebugKitDebugger::stopTimer('controllerAction');
		$vars = array();
		$panels = array_keys($this->panels);

		foreach ($panels as $panelName) {
			$panel =& $this->panels[$panelName];
			$vars[$panelName]['content'] = $panel->beforeRender($controller);
			$elementName = Inflector::underscore($panelName) . '_panel';
			if (isset($panel->elementName)) {
				$elementName = $panel->elementName;
			}
			$vars[$panelName]['elementName'] = $elementName;
			$vars[$panelName]['plugin'] = $panel->plugin;
			$vars[$panelName]['disableTimer'] = true;
		}
		$controller->set('debugToolbarPanels', $vars);
		DebugKitDebugger::startTimer('controllerRender', __('Render Action', true));
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
				trigger_error(sprintf(__('Could not load DebugToolbar panel %s', true), $panel), E_USER_WARNING);
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
 * Defines which plugin this panel is from so the element can be located.
 *
 * @var string
 */
	var $plugin = null;
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
	var $plugin = 'debug_kit';
	
	function beforeRender(&$controller) {
		return $controller->Session->read();
	}
}

/**
 * Request Panel
 *
 * Provides debug information on the Current request params.
 *
 * @package cake.debug_kit.panels
 **/
class RequestPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * beforeRender callback - grabs request params
 *
 * @return array
 **/
	function beforeRender(&$controller) {
		return $controller->params;
	}
}
/**
 * Timer Panel
 *
 * Provides debug information on all timers used in a request.
 *
 * @package cake.debug_kit.panels
 **/
class TimerPanel extends DebugPanel {
	var $plugin = 'debug_kit';
}
/**
 * Memory Panel
 *
 * Provides debug information on the memory consumption.
 *
 * @package cake.debug_kit.panels
 **/
class MemoryPanel extends DebugPanel {
	var $plugin = 'debug_kit';
}

/**
 * sqlLog Panel
 *
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 * @package cake.debug_kit.panels
 **/
class sqlLogPanel extends DebugPanel {
	var $plugin = 'debug_kit';
	
/**
 * get db configs.
 *
 * @param string $controller 
 * @access public
 * @return void
 */
	function startUp(&$controller) {
		if (!class_exists('ConnectionManager')) {
			$this->dbConfigs = array();
			return false;
		}
		$this->dbConfigs = ConnectionManager::sourceList();
		return true;
	}

/**
 * Get Sql Logs for each DB config
 *
 * @param string $controller 
 * @access public
 * @return void
 */
	function beforeRender(&$controller) {
		$queryLogs = array();
		if (!class_exists('ConnectionManager')) {
			return array();
		}
		foreach ($this->dbConfigs as $configName) {
			$db =& ConnectionManager::getDataSource($configName);
			if ($db->isInterfaceSupported('showLog')) {
				ob_start();
				$db->showLog();
				$queryLogs[$configName] = ob_get_clean();
			}
		}
		return $queryLogs;
	}
}

?>