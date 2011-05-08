<?php
/**
 * Abstract Toolbar helper.  Provides Base methods for content
 * specific debug toolbar helpers.  Acts as a facade for other toolbars helpers as well.
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
 * @subpackage    debug_kit.views.helpers
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Vendor', 'DebugKit.DebugKitDebugger');

class ToolbarHelper extends AppHelper {
/**
 * settings property to be overloaded.  Subclasses should specify a format
 *
 * @var array
 * @access public
 */
	public $settings = array();
/**
 * flag for whether or not cache is enabled.
 *
 * @var boolean
 **/
	protected $_cacheEnabled = false;
/**
 * Construct the helper and make the backend helper.
 *
 * @param string $options
 * @access public
 * @return void
 */
	public function __construct($View, $options = array()) {

		$this->_myName = strtolower(get_class($this));
		$this->settings = array_merge($this->settings, $options);

		if ($this->_myName !== 'toolbarhelper') {
			parent::__construct($View, $options);
			return;
		}

		if (!isset($options['output'])) {
			$options['output'] = 'DebugKit.HtmlToolbar';
		}
		App::import('Helper', $options['output']);
		$className = $options['output'];
		if (strpos($options['output'], '.') !== false) {
			list($plugin, $className) = explode('.', $options['output']);
		}
		$this->_backEndClassName = $className;
		$this->helpers[$options['output']] = $options;
		if (isset($options['cacheKey']) && isset($options['cacheConfig'])) {
			$this->_cacheKey = $options['cacheKey'];
			$this->_cacheConfig = $options['cacheConfig'];
			$this->_cacheEnabled = true;
		}

		parent::__construct($View, $options);

	}
/**
 * Get the name of the backend Helper
 * used to conditionally trigger toolbar output
 *
 * @return string
 **/
	public function getName() {
		return $this->_backEndClassName;
	}
/**
 * call__
 *
 * Allows method calls on backend helper
 *
 * @param string $method
 * @param mixed $params
 * @access public
 * @return void
 */
	public function __call($method, $params) {
		if (method_exists($this->{$this->_backEndClassName}, $method)) {
			return $this->{$this->_backEndClassName}->dispatchMethod($method, $params);
		}
	}
/**
 * Allows for writing to panel cache from view.
 * Some panels generate all variables in the view by
 * necessity ie. Timer.  Using this method, will allow you to replace in full
 * the content for a panel.
 *
 * @param string $name Name of the panel you are replacing.
 * @param string $content Content to write to the panel.
 * @return boolean Sucess of write.
 **/
	public function writeCache($name, $content) {
		if (!$this->_cacheEnabled) {
			return false;
		}
		$existing = (array)Cache::read($this->_cacheKey, $this->_cacheConfig);
		$existing[0][$name]['content'] = $content;
		return Cache::write($this->_cacheKey, $existing, $this->_cacheConfig);
	}
/**
 * Read the toolbar
 *
 * @param string $name Name of the panel you want cached data for
 * @return mixed Boolean false on failure, array of data otherwise.
 **/
	public function readCache($name, $index = 0) {
		if (!$this->_cacheEnabled) {
			return false;
		}
		$existing = (array)Cache::read($this->_cacheKey, $this->_cacheConfig);
		if (!isset($existing[$index][$name]['content'])) {
			return false;
		}
		return $existing[$index][$name]['content'];
	}
/**
 * Gets the query logs for the given connection names.
 *
 * ### Options
 *
 * - explain - Whether explain links should be generated for this connection.
 * - cache - Whether the toolbar_state Cache should be updated.
 * - threshold - The threshold at which a visual 'maybe slow' flag should be added.
 *   results with rows/ms lower than $threshold will be marked.
 *
 * @param string $connection Connection name to get logs for.
 * @param array $options Options for the query log retrieval.
 * @return array Array of data to be converted into a table.
 */
	public function getQueryLogs($connection, $options = array()) {
		$options += array('explain' => false, 'cache' => true, 'threshold' => 20);
		App::import('Model', 'ConnectionManager');
		$db = ConnectionManager::getDataSource($connection);
		
		$out = array();
		$log = $db->getLog();
		foreach ($log['log'] as $i => $query) {
			$isSlow = (
				$query['took'] > 0 &&
				$query['numRows'] / $query['took'] != 1 &&
				$query['numRows'] / $query['took'] <= $options['threshold']
			);
			$query['actions'] = '';
			$isHtml = ($this->getName() == 'HtmlToolbar');
			if ($isSlow && $isHtml) {
				$query['actions'] = sprintf(
					'<span class="slow-query">%s</span>',
					__d('debug_kit', 'maybe slow')
				);
			} elseif ($isSlow) {
				$query['actions'] = '*';
			}
			if ($options['explain'] && $isHtml) {
				$query['actions'] .= $this->explainLink($query['query'], $connection);
			}
			if ($isHtml) {
				$query['query'] = h($query['query']);
			}
			$out[] = $query;
		}
		if ($options['cache']) {
			$existing = $this->readCache('sql_log');
			$existing[$connection] = $out;
			$this->writeCache('sql_log', $existing);
		}
		return $out;
	}
}