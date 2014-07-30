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
namespace DebugKit\Panel;

use Cake\Controller\Controller;
use Cake\Log\Log;
use DebugKit\DebugPanel;

/**
 * Log Panel - Reads log entries made this request.
 *
 */
class LogPanel extends DebugPanel {

/**
 * Constructor - sets up the log listener.
 *
 * @return \LogPanel
 */
	public function __construct() {
		parent::__construct();
		$existing = Log::configured();
		if (empty($existing)) {
			Log::config('default', array(
				'engine' => 'FileLog'
			));
		}
		Log::config('debug_kit_log_panel', array(
			'engine' => 'DebugKit.DebugKitLog',
			'panel' => $this
		));
	}

/**
 * beforeRender Callback
 *
 * @param Controller $controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$logger = $this->logger;
		return $logger;
	}
}
