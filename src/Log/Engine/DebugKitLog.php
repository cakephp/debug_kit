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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Log\Engine;

use Cake\Log\LogInterface;

/**
 * A CakeLog listener which saves having to munge files or other configured loggers.
 *
 */
class DebugKitLog implements LogInterface {

/**
 * logs
 *
 * @var array
 */
	public $logs = array();

/**
 * Captures log messages in memory
 *
 * @param string $type The type of message being logged.
 * @param string $message The message being logged.
 * @param array $scope The scope
 * @return void
 */
	public function write($type, $message, $scope = []) {
		if (!isset($this->logs[$type])) {
			$this->logs[$type] = array();
		}
		$this->logs[$type][] = array(date('Y-m-d H:i:s'), (string)$message);
	}
}
