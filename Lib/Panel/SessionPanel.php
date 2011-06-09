<?php

/**
 * Session Panel
 *
 * Provides debug information on the Session contents.
 *
 * @package       cake.debug_kit.panels
 **/
class SessionPanel extends DebugPanel {

	public $plugin = 'debug_kit';

/**
 * beforeRender callback
 *
 * @param object $controller
 * @access public
 * @return array
 */
	public function beforeRender($controller) {
		$sessions = $controller->Toolbar->Session->read();
		return $sessions;
	}
}
