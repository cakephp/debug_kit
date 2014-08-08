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
 * Provides debug information on the View variables.
 *
 */
class VariablesPanel extends DebugPanel {

/**
 * beforeRender callback
 *
 * @param Controller $controller The controller.
 * @return array
 */
	public function beforeRender(Controller $controller) {
		return array_merge($controller->viewVars, array('$request->data' => $controller->request->data));
	}

/**
 * Shutdown event
 *
 * @param \Cake\Event\Event $event The event
 * @return void
 */
	public function shutdown(Event $event) {
		$controller = $event->subject();
		$request = $event->data['request'];
		$this->_data = array_merge(
			$controller->viewVars,
			['$request->data' => $request->data]
		);
	}
}
