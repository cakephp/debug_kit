<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Include Panel
 *
 * Provides a list of included files for the current request
 *
 * @package       cake.debug_kit.panels
 **/
class IncludePanel extends DebugPanel {

/**
 * The list of plugins within the application
 * @var <type>
 */
	protected $_pluginPaths = array();

	protected $_fileTypes = array(
		'Cache', 'Config', 'Configure', 'Console', 'Component', 'Controller',
		'Behavior', 'Datasource', 'Model', 'Plugin', 'Test', 'View', 'Utility',
		'Network', 'Routing', 'I18n', 'Log', 'Error'
	);

/**
 * Get a list of plugins on construct for later use
 */
	public function  __construct() {
		foreach (CakePlugin::loaded() as $plugin) {
			$this->_pluginPaths[$plugin] = CakePlugin::path($plugin);
		}

		parent::__construct();
	}

/**
 * Get a list of files that were included and split them out into the various parts of the app
 *
 * @param Controller $controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$return = array('core' => array(), 'app' => array(), 'plugins' => array());

		foreach (get_included_files() as $file) {
			$pluginName = $this->_isPluginFile($file);

			if ($pluginName) {
				$return['plugins'][$pluginName][$this->_getFileType($file)][] = $this->_niceFileName($file, $pluginName);
			} else if ($this->_isAppFile($file)) {
				$return['app'][$this->_getFileType($file)][] = $this->_niceFileName($file, 'app');
			} else if ($this->_isCoreFile($file)) {
				$return['core'][$this->_getFileType($file)][] = $this->_niceFileName($file, 'core');
			}
		}

		$return['paths'] = $this->_includePaths();

		ksort($return['core']);
		ksort($return['plugins']);
		ksort($return['app']);
		return $return;
	}

/**
 * Get the possible include paths
 * @return array
 */
	protected function _includePaths() {
		$split = (strstr(PHP_OS, 'win')) ? ';' : ':';
		$paths = array_flip(array_merge(explode($split, get_include_path()), array(CAKE)));

		unset($paths['.']);
		return array_flip($paths);
	}

/**
 * Check if a path is part of cake core
 * @param string $file
 * @return bool
 */
	protected function _isCoreFile($file) {
		return strstr($file, CAKE);
	}

/**
 * Check if a path is from APP but not a plugin
 * @param string $file
 * @return bool
 */
	protected function _isAppFile($file) {
		return strstr($file, APP);
	}

/**
 * Check if a path is from a plugin
 * @param string $file
 * @return bool
 */
	protected function _isPluginFile($file) {
		foreach ($this->_pluginPaths as $plugin => $path) {
			if (strstr($file, $path)) {
				return $plugin;
			}
		}

		return false;
	}

/**
 * Replace the path with APP, CORE or the plugin name
 * @param string $file
 * @param string
 *  - app for app files
 *  - core for core files
 *  - PluginName for the name of a plugin
 * @return bool
 */
	protected function _niceFileName($file, $type) {
		switch($type) {
			case 'app':
				return str_replace(APP, 'APP/', $file);

			case 'core':
				return str_replace(CAKE, 'CORE/', $file);

			default:
				return str_replace($this->_pluginPaths[$type], $type . '/', $file);
		}
	}

/**
 * Get the type of file (model, controller etc)
 * @param string $file
 * @return string
 */
	protected function _getFileType($file) {
		foreach ($this->_fileTypes as $type) {
			if (preg_match(sprintf('/%s/i', $type), $file)) {
				return $type;
			}
		}

		return 'Other';
	}
}
