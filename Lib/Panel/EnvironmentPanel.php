<?php

App::uses ('DebugPanel', 'DebugKit.Lib');

/**
 * Environment Panel
 *
 * Provides information about your PHP and CakePHP environment to assist with debugging.
 *
 * @package       cake.debug_kit.panels
 */
class EnvironmentPanel extends DebugPanel {

/**
 * beforeRender - Get necessary data about environment to pass back to controller
 *
 * @return array
 */
	public function beforeRender(Controller $controller) {
		parent::beforeRender ($controller);

		$return = array();

		// PHP Data
		$phpVer = phpversion();
		$return['php'] = array_merge(array('PHP_VERSION' => $phpVer), $_SERVER);
		unset($return['php']['argv']);

		// CakePHP Data
		$return['cake'] = array(
			'APP' => APP,
			'APP_DIR' => APP_DIR,
			'APPLIBS' => APPLIBS,
			'CACHE' => CACHE,
			'CAKE' => CAKE,
			'CAKE_CORE_INCLUDE_PATH' => CAKE_CORE_INCLUDE_PATH,
			'CORE_PATH' => CORE_PATH,
			'CAKE_VERSION' => Configure::version (),
			'CSS' => CSS,
			'CSS_URL' => CSS_URL,
			'DS' => DS,
			'FULL_BASE_URL' => FULL_BASE_URL,
			'IMAGES' => IMAGES,
			'IMAGES_URL' => IMAGES_URL,
			'JS' => JS,
			'JS_URL' => JS_URL,
			'LOGS' => LOGS,
			'ROOT' => ROOT,
			'TESTS' => TESTS,
			'TMP' => TMP,
			'VENDORS' => VENDORS,
			'WEBROOT_DIR' => WEBROOT_DIR,
			'WWW_ROOT' => WWW_ROOT
		);

		$cakeConstants = array_fill_keys(array('DS', 'ROOT', 'FULL_BASE_URL', 'TIME_START', 'SECOND',
			 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR', 'LOG_ERROR', 'FULL_BASE_URL'), '');
		$var = get_defined_constants(true);
		$return['app'] = array_diff_key($var['user'], $return['cake'], $cakeConstants);

		if (isset($var['hidef'])) {
			$return['hidef'] = $var['hidef'];
		}

		return $return;
	}

}
