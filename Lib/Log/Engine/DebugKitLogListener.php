<?php
/**
 * A CakeLog listener which saves having to munge files or other configured loggers.
 *
 * @package debug_kit.components
 */

class DebugKitLogListener implements CakeLogInterface {

	public $logs = array();

/**
 * Makes the reverse link needed to get the logs later.
 *
 * @return void
 */
	public function __construct($options) {
		$options['panel']->logger = $this;
	}

/**
 * Captures log messages in memory
 *
 * @return void
 */
	public function write($type, $message) {
		if (!isset($this->logs[$type])) {
			$this->logs[$type] = array();
		}
		$this->logs[$type][] = array(date('Y-m-d H:i:s'), $message);
	}
}
