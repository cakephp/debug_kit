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
namespace Cake\DebugKit\Panel;

use Cake\Controller\Controller;
use Cake\DebugKit\DebugPanel;
use Cake\Event\Event;

/**
 * Provides debug information on the Session contents.
 *
 */
class SessionPanel extends DebugPanel {

/**
 * beforeRender callback
 *
 * @param \Cake\Controller\Controller $controller The controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$sessions = $controller->Toolbar->Session->read();
		return $sessions;
	}

/**
 * shutdown callback
 *
 * @param \Cake\Event\Event $event The event
 * @return array
 */
	public function shutdown(Event $event) {
		$request = $event->data['request'];
		if ($request) {
			$this->_data = $request->session()->read();
		}
	}
}
