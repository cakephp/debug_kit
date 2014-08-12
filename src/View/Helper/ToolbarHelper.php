<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\View\Helper;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Cache\Cache;
use Cake\View\Helper;
use DebugKit\DebugKitDebugger;

/**
 * Provides Base methods for content specific debug toolbar helpers.
 * Acts as a facade for other toolbars helpers as well.
 *
 * @since         DebugKit 0.1
 */
class ToolbarHelper extends Helper {

/**
 * settings property to be overloaded. Subclasses should specify a format
 *
 * @var array
 */
	public $settings = array();

/**
 * flag for whether or not cache is enabled.
 *
 * @var boolean
 */
	protected $_cacheEnabled = false;

/**
 * Construct the helper and make the backend helper.
 *
 * @param $View
 * @param array|string $options
 * @return \ToolbarHelper
 */
	public function __construct($View, $options = array()) {
		$this->_myName = get_class($this);
		$this->settings = array_merge($this->settings, $options);

		if ($this->_myName !== 'DebugKit\View\Helper\ToolbarHelper') {
			parent::__construct($View, $options);
			return;
		}

		if (!isset($options['output'])) {
			$options['output'] = 'DebugKit.HtmlToolbar';
		}
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
 * afterLayout callback
 *
 * @param \Cake\Event\Event $event The event
 * @param string $layoutFile
 * @return void
 */
	public function afterLayout(Event $event, $layoutFile) {
		if (!$this->request->is('requested')) {
			$this->send();
		}
	}

/**
 * Get the name of the backend Helper
 * used to conditionally trigger toolbar output
 *
 * @return string
 */
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
 * @return mixed|void
 */
	public function __call($method, $params) {
		if (method_exists($this->{$this->_backEndClassName}, $method)) {
			return call_user_func_array(
				[$this->{$this->_backEndClassName}, $method],
				$params
			);
		}
	}

/**
 * Allows for writing to panel cache from view.
 * Some panels generate all variables in the view by
 * necessity ie. Timer. Using this method, will allow you to replace in full
 * the content for a panel.
 *
 * @param string $name Name of the panel you are replacing.
 * @param string $content Content to write to the panel.
 * @return boolean Success of write.
 */
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
 * @param integer $index
 * @return mixed Boolean false on failure, array of data otherwise.
 */
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

}
