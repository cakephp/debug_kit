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
 * @package       cake
 * @subpackage    cake.cake.libs.
 * @since         CakePHP v 1.2.0.4487
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ToolbarComponent extends Object {
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
 * The default panels the toolbar uses.
 * which panels are used can be configured when attaching the component
 *
 * @var array
 */
	var $_defaultPanels = array('session', 'request', 'sqlLog', 'timer', 'log', 'memory', 'variables');
/**
 * Loaded panel objects.
 *
 * @var array
 */
	var $panels = array();

/**
 * fallback for javascript settings
 *
 * @var array
 **/
	var $_defaultJavascript = array(
		'behavior' => '/debug_kit/js/js_debug_toolbar'
	);
/**
 * javascript files component will be using.
 *
 * @var array
 **/
	var $javascript = array();
/**
 * initialize
 *
 * If debug is off the component will be disabled and not do any further time tracking
 * or load the toolbar helper.
 *
 * @return bool
 **/
	function initialize(&$controller, $settings) {
		if (Configure::read('debug') == 0) {
			$this->enabled = false;
			return false;
		}
		App::import('Vendor', 'DebugKit.DebugKitDebugger');
		
		DebugKitDebugger::startTimer('componentInit', __('Component initialization and startup', true));
		if (!isset($settings['panels'])) {
			$settings['panels'] = $this->_defaultPanels;
		}

		if (isset($settings['javascript'])) {
			$settings['javascript'] = $this->_setJavascript($settings['javascript']);
		} else {
			$settings['javascript'] = $this->_defaultJavascript;
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
		$currentViewClass = $controller->view;
		$this->_makeViewClass($currentViewClass);
		$controller->view = 'DebugKit.Debug';
		if (!isset($controller->params['url']['ext']) || (isset($controller->params['url']['ext']) && $controller->params['url']['ext'] == 'html')) {
			$format = 'Html';
		} else {
			$format = 'FirePhp';
		}
		$controller->helpers['DebugKit.Toolbar'] = array('output' => sprintf('DebugKit.%sToolbar', $format));
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

		$controller->set(array('debugToolbarPanels' => $vars, 'debugToolbarJavascript' => $this->javascript));
		DebugKitDebugger::startTimer('controllerRender', __('Render Controller Action', true));
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
			if (!class_exists($className) && !App::import('Vendor',  $className)) {
				trigger_error(sprintf(__('Could not load DebugToolbar panel %s', true), $panel), E_USER_WARNING);
				continue;
			}
			$panelObj =& new $className();
			if (is_subclass_of($panelObj, 'DebugPanel') || is_subclass_of($panelObj, 'debugpanel')) {
				$this->panels[$panel] =& $panelObj;
			}
		}
	}

/**
 * Set the javascript to user scripts.
 *
 * Set either script key to false to exclude it from the rendered layout.
 *
 * @param array $scripts Javascript config information
 * @return array
 * @access protected
 **/
	function _setJavascript($scripts) {
		$behavior = false;
		if (!is_array($scripts)) {
			$scripts = (array)$scripts;
		}
		if (isset($scripts[0])) {
			$behavior = $scripts[0];
		}
		if (isset($scripts['behavior'])) {
			$behavior = $scripts['behavior'];
		}
		if (!$behavior) {
			return array();
		} elseif ($behavior === true) {
			$behavior = 'js';
		}
		if (strpos($behavior, '/') !== 0) {
			$behavior .= '_debug_toolbar';
		}
		$pluginFile = APP . 'plugins' . DS . 'debug_kit' . DS . 'vendors' . DS . 'js' . DS . $behavior . '.js';
		if (file_exists($pluginFile)) {
			$behavior = '/debug_kit/js/' . $behavior . '.js';
		}
		return compact('behavior');
	}
/**
 * Makes the DoppleGangerView class if it doesn't already exist.
 * This allows DebugView to be compatible with all view classes.
 *
 * @param string $baseClassName 
 * @access protected
 * @return void
 */
	function _makeViewClass($baseClassName) {
		if (!class_exists('DoppelGangerView')) {
			App::import('View', $baseClassName);
			if (strpos('View', $baseClassName) === false) {
				$baseClassName .= 'View';
			}
			$class = "class DoppelGangerView extends $baseClassName {}";
			eval($class);
		}
	}
}

/**
 * Debug Panel
 *
 * Abstract class for debug panels.
 *
 * @package       cake.debug_kit
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
 * Variables Panel
 *
 * Provides debug information on the View variables.
 *
 * @package       cake.debug_kit.panels
 **/
class VariablesPanel extends DebugPanel {
	var $plugin = 'debug_kit';
}

/**
 * Session Panel
 *
 * Provides debug information on the Session contents.
 *
 * @package       cake.debug_kit.panels
 **/
class SessionPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * beforeRender callback
 *
 * @param object $controller
 * @access public
 * @return array
 */
	function beforeRender(&$controller) {
		return $controller->Session->read();
	}
}

/**
 * Request Panel
 *
 * Provides debug information on the Current request params.
 *
 * @package       cake.debug_kit.panels
 **/
class RequestPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * beforeRender callback - grabs request params
 *
 * @return array
 **/
	function beforeRender(&$controller) {
		$out = array();
		$out['params'] = $controller->params;
		if (isset($controller->Cookie)) {
			$out['cookie'] = $controller->Cookie->read();
		}
		$out['get'] = $_GET;
		$out['currentRoute'] = Router::currentRoute();
		return $out;
	}
}

/**
 * Timer Panel
 *
 * Provides debug information on all timers used in a request.
 *
 * @package       cake.debug_kit.panels
 **/
class TimerPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * startup - add in necessary helpers
 *
 * @return void
 **/
	function startup(&$controller) {
		if (!in_array('Number', $controller->helpers)) {
			$controller->helpers[] = 'Number';
		}
	}
}

/**
 * Memory Panel
 *
 * Provides debug information on the memory consumption.
 *
 * @package       cake.debug_kit.panels
 **/
class MemoryPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * startup - add in necessary helpers
 *
 * @return void
 **/
	function startup(&$controller) {
		if (!in_array('Number', $controller->helpers)) {
			$controller->helpers[] = 'Number';
		}
	}
}

/**
 * sqlLog Panel
 *
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 * @package       cake.debug_kit.panels
 **/
class sqlLogPanel extends DebugPanel {
	var $plugin = 'debug_kit';

	var $dbConfigs = array();
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

/**
 * Log Panel - Reads log entries made this request.
 *
 * @package       cake.debug_kit.panels
 */
class LogPanel extends DebugPanel {
	var $plugin = 'debug_kit';
/**
 * Log files to scan
 *
 * @var array
 */
	var $logFiles = array('error.log', 'debug.log');
/**
 * startup
 *
 * @return void
 **/
	function startup(&$controller) {
		if (!class_exists('CakeLog')) {
			App::import('Core', 'Log');
		}
	}
/**
 * beforeRender Callback
 *
 * @return array
 **/
	function beforeRender(&$controller) {
		$this->startTime = DebugKitDebugger::requestStartTime();
		$this->currentTime = DebugKitDebugger::requestTime();
		$out = array();
		foreach ($this->logFiles as $log) {
			$file = LOGS . $log;
			if (!file_exists($file)) {
				continue;
			}
			$out[$log] = $this->_parseFile($file);
		}
		return $out;
	}
/**
 * parse a log file and find the relevant entries
 *
 * @param string $filename Name of file to read
 * @access protected
 * @return array
 */
	function _parseFile($filename) {
		$file =& new File($filename);
		$contents = $file->read();
		$timePattern = '/(\d{4}-\d{2}\-\d{2}\s\d{1,2}\:\d{1,2}\:\d{1,2})/';
		$chunks = preg_split($timePattern, $contents, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $len = count($chunks); $i < $len; $i += 2) {
			if (strtotime($chunks[$i]) < $this->startTime) {
				unset($chunks[$i], $chunks[$i + 1]);
			}
		}
		return array_values($chunks);
	}
}
?>