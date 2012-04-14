<?php
App::uses('DebugKitLogListener', 'DebugKit.Log/Engine');
class_exists('DebugKitLogListener');

/**
 * Log Panel - Reads log entries made this request.
 *
 * @package       cake.debug_kit.panels
 */
class LogPanel extends DebugPanel {
	
/**
 * Constructor - sets up the log listener.
 *
 * @return void
 */
	public function __construct($settings) {
		parent::__construct();
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
 */
	public function beforeRender(Controller $controller) {
		$logger = $this->logger;
		return $logger;
	}
}