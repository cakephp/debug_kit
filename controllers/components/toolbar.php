<?php
/**
 * DebugKit DebugToolbar Component
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
 * @subpackage    debug_kit.controllers.components
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
class ToolbarComponent extends Object {
/**
 * Settings for the Component
 *
 * - forceEnable - Force the toolbar to display even if debug == 0. Default = false
 * - autoRun - Automatically display the toolbar. If set to false, toolbar display can be triggered by adding
 *    `?debug=true` to your URL.
 *
 * @var array
 **/
	var $settings = array(
		'forceEnable' => false,
		'autoRun' => true
	);

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
	var $components = array('RequestHandler', 'Session');

/**
 * The default panels the toolbar uses.
 * which panels are used can be configured when attaching the component
 *
 * @var array
 */
	var $_defaultPanels = array('history', 'session', 'request', 'sqlLog', 'timer', 'log', 'variables');

/**
 * Loaded panel objects.
 *
 * @var array
 */
	var $panels = array();

/**
 * javascript files component will be using
 *
 * @var array
 **/
	var $javascript = array(
		'behavior' => '/debug_kit/js/js_debug_toolbar'
	);

/**
 * CacheKey used for the cache file.
 *
 * @var string
 **/
	var $cacheKey = 'toolbar_cache';

/**
 * Duration of the debug kit history cache
 *
 * @var string
 **/
	var $cacheDuration = '+4 hours';

/**
 * initialize
 *
 * If debug is off the component will be disabled and not do any further time tracking
 * or load the toolbar helper.
 *
 * @return bool
 **/
	function initialize(&$controller, $settings) {
		$this->settings = am($this->settings, $settings);
		if (!Configure::read('debug') && empty($this->settings['forceEnable'])) {
			$this->enabled = false;
			return false;
		}
		if ($this->settings['autoRun'] == false && !isset($controller->params['url']['debug'])) {
			$this->enabled = false;
			return false;
		}
		App::import('Vendor', 'DebugKit.DebugKitDebugger');

		DebugKitDebugger::setMemoryPoint(__d('debug_kit', 'Component initialization', true));
		DebugKitDebugger::startTimer('componentInit', __d('debug_kit', 'Component initialization and startup', true));

		$panels = $this->_defaultPanels;
		if (isset($settings['panels'])) {
			$panels = $this->_makePanelList($settings['panels']);
			unset($settings['panels']);
		}

		$this->cacheKey .= $this->Session->read('Config.userAgent');
		if (in_array('history', $panels) || (isset($settings['history']) && $settings['history'] !== false)) {
			$this->_createCacheConfig();
		}

		$this->_loadPanels($panels, $settings);

		$this->_set($settings);
		$this->controller =& $controller;
		return false;
	}

/**
 * Go through user panels and remove default panels as indicated.
 *
 * @param array $userPanels The list of panels ther user has added removed.
 * @return array Array of panels to use.
 **/
	function _makePanelList($userPanels) {
		$panels = $this->_defaultPanels;
		foreach ($userPanels as $key => $value) {
			if (is_numeric($key)) {
				$panels[] = $value;
			}
			if (is_string($key) && $value === false) {
				$index = array_search($key, $panels);
				if ($index !== false) {
					unset($panels[$index]);
				}
			}
		}
		return $panels;
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
		$isHtml = (
			!isset($controller->params['url']['ext']) ||
			(isset($controller->params['url']['ext']) && $controller->params['url']['ext'] == 'html')
		);

		if (!$this->RequestHandler->isAjax() && $isHtml) {
			$format = 'Html';
		} else {
			$format = 'FirePhp';
		}
		$controller->helpers['DebugKit.Toolbar'] = array(
			'output' => sprintf('DebugKit.%sToolbar', $format),
			'cacheKey' => $this->cacheKey,
			'cacheConfig' => 'debug_kit',
			'forceEnable' => $this->settings['forceEnable'],
		);
		$panels = array_keys($this->panels);
		foreach ($panels as $panelName) {
			$this->panels[$panelName]->startup($controller);
		}
		DebugKitDebugger::stopTimer('componentInit');
		DebugKitDebugger::startTimer('controllerAction', __d('debug_kit', 'Controller action', true));
		DebugKitDebugger::setMemoryPoint(__d('debug_kit', 'Controller action start', true));
	}

/**
 * beforeRedirect callback
 *
 * @return void
 **/
	function beforeRedirect(&$controller) {
		if (!class_exists('DebugKitDebugger')) {
			return null;
		}
		DebugKitDebugger::stopTimer('controllerAction');
		$vars = $this->_gatherVars($controller);
		$this->_saveState($controller, $vars);
	}

/**
 * beforeRender callback
 *
 * Calls beforeRender on all the panels and set the aggregate to the controller.
 *
 * @return void
 **/
	function beforeRender(&$controller) {
		if (!class_exists('DebugKitDebugger')) {
			return null;
		}
		DebugKitDebugger::stopTimer('controllerAction');
		$vars = $this->_gatherVars($controller);
		$this->_saveState($controller, $vars);

		$controller->set(array('debugToolbarPanels' => $vars, 'debugToolbarJavascript' => $this->javascript));
		DebugKitDebugger::startTimer('controllerRender', __d('debug_kit', 'Render Controller Action', true));
		DebugKitDebugger::setMemoryPoint(__d('debug_kit', 'Controller render start', true));
	}

/**
 * Load a toolbar state from cache
 *
 * @param int $key
 * @return array
 **/
	function loadState($key) {
		$history = Cache::read($this->cacheKey, 'debug_kit');
		if (isset($history[$key])) {
			return $history[$key];
		}
		return array();
	}

/**
 * Create the cache config for the history
 *
 * @return void
 * @access protected
 **/
	function _createCacheConfig() {
		if (Configure::read('Cache.disable') !== true) {
			Cache::config('debug_kit', array(
				'duration' => $this->cacheDuration,
				'engine' => 'File',
				'path' => CACHE
			));
			Cache::config('default');
		}
	}

/**
 * collects the panel contents
 *
 * @return array Array of all panel beforeRender()
 * @access protected
 **/
	function _gatherVars(&$controller) {
		$vars = array();
		$panels = array_keys($this->panels);

		foreach ($panels as $panelName) {
			$panel =& $this->panels[$panelName];
			$panelName = Inflector::underscore($panelName);
			$vars[$panelName]['content'] = $panel->beforeRender($controller);
			$elementName = Inflector::underscore($panelName) . '_panel';
			if (isset($panel->elementName)) {
				$elementName = $panel->elementName;
			}
			$vars[$panelName]['elementName'] = $elementName;
			$vars[$panelName]['plugin'] = $panel->plugin;
			$vars[$panelName]['title'] = $panel->title;
			$vars[$panelName]['disableTimer'] = true;
		}
		return $vars;
	}

/**
 * Load Panels used in the debug toolbar
 *
 * @return 	void
 * @access protected
 **/
	function _loadPanels($panels, $settings) {
		foreach ($panels as $panel) {
			$className = $panel . 'Panel';
			if (!class_exists($className) && !App::import('Vendor',  $className)) {
				trigger_error(sprintf(__d('debug_kit', 'Could not load DebugToolbar panel %s', true), $panel), E_USER_WARNING);
				continue;
			}
			list($plugin, $className) = pluginSplit($className);
			$panelObj =& new $className($settings);
			if (is_subclass_of($panelObj, 'DebugPanel') || is_subclass_of($panelObj, 'debugpanel')) {
				list(, $panel) = pluginSplit($panel);
				$this->panels[$panel] =& $panelObj;
			}
		}
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
			$parent = strtolower($baseClassName) === 'view' ? false : true;
			App::import('View', $baseClassName, $parent);
			if (strpos($baseClassName, '.') !== false) {
				list($plugin, $baseClassName) = explode('.', $baseClassName);
			}
			if (strpos($baseClassName, 'View') === false) {
				$baseClassName .= 'View';
			}
			$class = "class DoppelGangerView extends $baseClassName {}";
			$this->_eval($class);
		}
	}

/**
 * Method wrapper for eval() for testing uses.
 *
 * @return void
 **/
	function _eval($code) {
		eval($code);
	}

/**
 * Save the current state of the toolbar varibles to the cache file.
 *
 * @param object $controller Controller instance
 * @param array $vars Vars to save.
 * @access protected
 * @return void
 **/
	function _saveState(&$controller, $vars) {
		$config = Cache::config('debug_kit');
		if (empty($config) || !isset($this->panels['history'])) {
			return;
		}
		$history = Cache::read($this->cacheKey, 'debug_kit');
		if (empty($history)) {
			$history = array();
		}
		if (count($history) == $this->panels['history']->history) {
			array_pop($history);
		}
		unset($vars['history']);
		array_unshift($history, $vars);
		Cache::write($this->cacheKey, $history, 'debug_kit');
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
 * Defines the title for displaying on the toolbar. If null, the class name will be used.
 * Overriding this allows you to define a custom name in the toolbar.
 *
 * @var string
 */
	var $title = null;

/**
 * Provide a custom element name for this panel.  If null, the underscored version of the class
 * name will be used.
 *
 * @var string
 */
	var $elementName = null;

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
 * History Panel
 *
 * Provides debug information on previous requests.
 *
 * @package       cake.debug_kit.panels
 **/
class HistoryPanel extends DebugPanel {

	var $plugin = 'debug_kit';

/**
 * Number of history elements to keep
 *
 * @var string
 **/
	var $history = 5;

/**
 * Constructor
 *
 * @param array $settings Array of settings.
 * @return void
 **/
	function __construct($settings) {
		if (isset($settings['history'])) {
			$this->history = $settings['history'];
		}
	}

/**
 * beforeRender callback function
 *
 * @return array contents for panel
 **/
	function beforeRender(&$controller) {
		$cacheKey = $controller->Toolbar->cacheKey;
		$toolbarHistory = Cache::read($cacheKey, 'debug_kit');
		$historyStates = array();
		if (is_array($toolbarHistory) && !empty($toolbarHistory)) {
			$prefix = array();
			if (!empty($controller->params['prefix'])) {
				$prefix[$controller->params['prefix']] = false;
			}
			foreach ($toolbarHistory as $i => $state) {
				if (!isset($state['request']['content']['params']['url']['url'])) {
					continue;
				}
				$historyStates[] = array(
					'title' => $state['request']['content']['params']['url']['url'],
					'url' => array_merge($prefix, array(
						'plugin' => 'debug_kit',
						'controller' => 'toolbar_access',
						'action' => 'history_state',
						$i + 1))
				);
			}
		}
		if (count($historyStates) >= $this->history) {
			array_pop($historyStates);
		}
		return $historyStates;
	}
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

/**
 * beforeRender callback
 *
 * @return array
 **/
	function beforeRender(&$controller) {
		return array_merge($controller->viewVars, array('$this->data' => $controller->data));
	}
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
		$sessions = $controller->Toolbar->Session->read();
		return $sessions;
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
		if (!in_array('SimpleGraph', $controller->helpers)) {
			$controller->helpers[] = 'DebugKit.SimpleGraph';
		}
	}
}

/**
 * SqlLog Panel
 *
 * Provides debug information on the SQL logs and provides links to an ajax explain interface.
 *
 * @package       cake.debug_kit.panels
 **/
class SqlLogPanel extends DebugPanel {

	var $plugin = 'debug_kit';

/**
 * Minimum number of Rows Per Millisecond that must be returned by a query before an explain
 * is done.
 *
 * @var int
 **/
	var $slowRate = 20;

/**
 * Gets the connection names that should have logs + dumps generated.
 *
 * @param string $controller
 * @access public
 * @return void
 */
	function beforeRender(&$controller) {
		if (!class_exists('ConnectionManager')) {
			return array();
		}
		$connections = array();

		$dbConfigs = ConnectionManager::sourceList();
		foreach ($dbConfigs as $configName) {
			$driver = null;
			$db =& ConnectionManager::getDataSource($configName);
			if (
				(empty($db->config['driver']) && empty($db->config['datasource'])) ||
				!$db->isInterfaceSupported('getLog')
			) {
				continue;
			}

			if (isset($db->config['driver'])) {
				$driver = $db->config['driver'];
			}
			if (empty($driver) && isset($db->config['datasource'])) {
				$driver = $db->config['datasource'];
			}
			$explain = false;
			$isExplainable = ($driver === 'mysql' || $driver === 'mysqli' || $driver === 'postgres');
			if ($isExplainable) {
				$explain = true;
			}
			$connections[$configName] = $explain;
		}
		return array('connections' => $connections, 'threshold' => $this->slowRate);
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
 * Constructor - sets up the log listener.
 *
 * @return void
 */
	function __construct($settings) {
		parent::__construct();
		if (!class_exists('CakeLog')) {
			App::import('Core', 'CakeLog');
		}
		$existing = CakeLog::configured();
		if (empty($existing)) {
			CakeLog::config('default', array(
				'engine' => 'FileLog'
			));
		}
		CakeLog::config('debug_kit_log_panel', array(
			'engine' => 'DebugKitLogListener',
			'panel' => $this
		));
	}

/**
 * beforeRender Callback
 *
 * @return array
 **/
	function beforeRender(&$controller) {
		$logs = $this->logger->logs;
		return $logs;
	}
}

/**
 * A CakeLog listener which saves having to munge files or other configured loggers.
 *
 * @package debug_kit.components
 */
class DebugKitLogListener {

	var $logs = array();

/**
 * Makes the reverse link needed to get the logs later.
 *
 * @return void
 */
	function DebugKitLogListener($options) {
		$options['panel']->logger =& $this;
	}

/**
 * Captures log messages in memory
 *
 * @return void
 */
	function write($type, $message) {
		if (!isset($this->logs[$type])) {
			$this->logs[$type] = array();
		}
		$this->logs[$type][] = array(date('Y-m-d H:i:s'), $message);
	}
}

