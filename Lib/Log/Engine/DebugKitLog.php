<?php
/**
 * A CakeLog listener which saves having to munge files or other configured loggers.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       DebugKit.Lib.Log.Engine
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Class DebugKitLog
 *
 * @package       DebugKit.Lib.Log.Engine
 */
class DebugKitLog implements CakeLogInterface {

/**
 * logs
 *
 * @var array
 */
	public $logs = array();

/**
 * Makes the reverse link needed to get the logs later.
 *
 * @param $options
 * @return \DebugKitLog
 */
	public function __construct($options) {
		$options['panel']->logger = $this;
	}

/**
 * Captures log messages in memory
 *
 * @param $type
 * @param $message
 * @return void
 */
	public function write($type, $message) {
		if (!isset($this->logs[$type])) {
			$this->logs[$type] = array();
		}
		$this->logs[$type][] = array(date('Y-m-d H:i:s'), $message);
	}
}
