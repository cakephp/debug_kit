<?php
App::uses('DebugPanel', 'DebugKit.Lib');

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
	public function __construct() {
		parent::__construct();
		$existing = CakeLog::configured();
		if (empty($existing)) {
			CakeLog::config('default', array(
				'engine' => 'FileLog'
			));
		}
		CakeLog::config('debug_kit_log_panel', array(
			'engine' => 'DebugKit.DebugKitLogListener',
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
