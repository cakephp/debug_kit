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

App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Provides debug information on the View variables.
 *
 */
class VariablesPanel extends DebugPanel {

	public $priority = 1;

/**
 * beforeRender callback
 *
 * @param Controller $controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		if ($this->priority > 0) {
			$this->title = __d('debug_kit', '<b>%d</b> vars', (count(array_diff(array_keys($controller->viewVars), array('title_for_layout')))));
		}

		return array_merge($controller->viewVars, array('$request->data' => $controller->request->data));
	}
}
