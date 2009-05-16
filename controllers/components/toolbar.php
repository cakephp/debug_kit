<?php
/**
 * DebugKit DebugToolbar Component
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.controllers.components
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
class ToolbarComponent extends Object {
	var $settings = array();
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
		App::import('Vendor', 'DebugKit.DebugKitDebugger');

		DebugKitDebugger::startTimer('componentInit', __('Component initialization and startup', true));

		$panels = $this->_defaultPanels;
		if (isset($settings['panels'])) {
			$panels = $settings['panels'];
			unset($settings['panels']);
		}
		$this->cacheKey .= $controller->Session->read('Config.userAgent');
		if (!isset($settings['history']) || (isset($settings['history']) && $settings['history'] !== false)) {
			$this->_createCacheConfig();
		}

		$this->_loadPanels($panels, $settings);

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
			'forceEnable' => isset($this->settings['forceEnable'])? true : null,
		);
		$panels = array_keys($this->panels);
		foreach ($panels as $panelName) {
			$this->panels[$panelName]->startup($controller);
		}
		DebugKitDebugger::stopTimer('componentInit');
		DebugKitDebugger::startTimer('controllerAction', __('Controller Action', true));
	}

/**
 * beforeRedirect callback
 *
 * @return void
 **/
	function beforeRedirect(&$controller) {
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
		DebugKitDebugger::stopTimer('controllerAction');
		$vars = $this->_gatherVars($controller);
		$this->_saveState($controller, $vars);

		$controller->set(array('debugToolbarPanels' => $vars, 'debugToolbarJavascript' => $this->javascript));
		DebugKitDebugger::startTimer('controllerRender', __('Render Controller Action', true));
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
			Cache::config('debug_kit', array('duration' => $this->cacheDuration, 'engine' => 'File'));
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
				trigger_error(sprintf(__('Could not load DebugToolbar panel %s', true), $panel), E_USER_WARNING);
				continue;
			}
			$panelObj =& new $className($settings);
			if (is_subclass_of($panelObj, 'DebugPanel') || is_subclass_of($panelObj, 'debugpanel')) {
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
			App::import('View', $baseClassName);
			if (strpos('View', $baseClassName) === false) {
				$baseClassName .= 'View';
			}
			$class = "class DoppelGangerView extends $baseClassName {}";
			eval($class);
		}
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
		$sessions = $controller->Session->read();
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
 * Get Sql Logs for each DB config
 *
 * @param string $controller
 * @access public
 * @return void
 */
	function beforeRender(&$controller) {
		if (!class_exists('ConnectionManager')) {
			return array();
		}
		App::import('Core', 'Xml');
		$queryLogs = array();

		$dbConfigs = ConnectionManager::sourceList();
		foreach ($dbConfigs as $configName) {
			$db =& ConnectionManager::getDataSource($configName);
			if ($db->isInterfaceSupported('showLog')) {
				ob_start();
				$db->showLog();
				$htmlBlob = ob_get_clean();

				$Xml =& new Xml($htmlBlob);

				$table = $Xml->children[0];
				$tbody = $table->children('tbody');
				$rows = $tbody[0]->children;
				if (empty($rows) || empty($rows[0]->children)) {
				 	continue;
				}
				$queries = $explained = array();
				foreach ($rows as $row) {
					$tds = $this->_getCells($row);
					$queries[] = $tds;
					$isSlow = (
						$tds[5] > 0 &&
						$tds[4] / $tds[5] != 1 &&
						$tds[4] / $tds[5] <= $this->slowRate
					);
					if ($isSlow && preg_match('/^SELECT /', $tds[1])) {
						$explain = $this->_explainQuery($db, $tds[1]);
						if (!empty($explain)) {
							$explained[] = $explain;
						}
					}
				}
				$queryLogs[$configName]['queries'] = $queries;
				$queryLogs[$configName]['explains'] = $explained;
			}
		}
		return $queryLogs;
	}
/**
 * get cell values from xml
 *
 * @param array of XmlElements.
 * @return array Array of extracted values.
 **/
	function _getCells($rowXml) {
		$tds = array();
		foreach ($rowXml->children as $cell) {
			if ($cell->hasChildren()) {
				$tds[] = $cell->children[0]->value;
			} else {
				$tds[] = $cell->value;
			}
		}
		return $tds;
	}
/**
 * Run an explain query for a slow query.
 *
 * @param object $db Dbo instance
 * @param string $queryString The Query to explain
 * @access public
 * @return void
 **/
	function _explainQuery(&$db, $queryString) {
		$driver = $db->config['driver'];
		$results = null;
		if ($driver === 'mysqli' || $driver === 'mysql' || $driver === 'postgres') {
			$results = $db->query('EXPLAIN ' . $queryString);
			if ($driver === 'postgres') {
				$queryPlan = array();
				foreach ($results as $postgreValue) {
					$queryPlan[] = $postgreValue[0]['QUERY PLAN'];
				}
				$results[0][0] = array('Query Plan' => implode("<br />", $queryPlan));
			}
			$results = $results[0][0];
			$results['query'] =  $queryString;
		}
		return $results;
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