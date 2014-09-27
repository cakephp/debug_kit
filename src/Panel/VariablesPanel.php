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
use Cake\Database\Query;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the View variables.
 *
 */
class VariablesPanel extends DebugPanel {

/**
 * Shutdown event
 *
 * @param \Cake\Event\Event $event The event
 * @return void
 */
	public function shutdown(Event $event) {
		$controller = $event->subject();
		$errors = [];
		$vars = $controller->viewVars;
		foreach ($vars as $k => $v) {
			// Execute queries so we can show the results in the toolbar.
			if ($v instanceof Query) {
				$vars[$k] = $v->all();
			} elseif ($v instanceof EntityInterface) {
				// Get the validation errors for Entity
				$errors[$k] = $v->errors();
			}
		}
		$this->_data = [
			'content' => $vars,
			'errors' => $errors
		];
	}
}
