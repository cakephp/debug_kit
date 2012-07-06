<?php

/**
 * Evironment Element
 *
 * PHP versions 5
 *
 * Copyright 2012 Scott Harwell
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Scott Harwell 
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/

App::uses ('DebugPanel', 'DebugKit.Lib');

/**
 * Environment Panel
 *
 * Provides information about your PHP and CakePHP environment to assist with debugging.
 *
 * @package       cake.debug_kit.panels
 */

class EnvironmentPanel extends DebugPanel
{

	/**
	 * beforeRender - Get necessary data about environment to pass back to controller
	 *
	 * @return array
	 */
	
	public function beforeRender (Controller $controller)
	{
		parent::beforeRender ($controller);
		
		$return = array();
		
		// PHP Data
		$phpVer = phpversion();
		$return['php'] = array_merge(array('PHP_VERSION' => $phpVer), $_SERVER);
		
		// CakePHP Data
		$return['cake'] = array(
			'APP_PATH' => APP,
			'CAKE_PATH' => CAKE,
			'CAKE_VERSION' => Configure::version ()
		);
		
		return $return;
	}
}
