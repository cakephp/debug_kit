<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Session Panel
 *
 * Provides debug information on the Session contents.
 *
 * @package       cake.debug_kit.panels
 */
class SessionPanel extends DebugPanel {

/**
 * beforeRender callback
 *
 * @param object $controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$sessions = $controller->Toolbar->Session->read();
		return $sessions;
	}
}
