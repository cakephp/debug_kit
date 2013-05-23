<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Variables Panel
 *
 * Provides debug information on the View variables.
 *
 * @package       cake.debug_kit.panels
 */
class VariablesPanel extends DebugPanel {

	public $priority = 1;

/**
 * beforeRender callback
 *
 * @return array
 */
	public function beforeRender(Controller $controller) {
		if ($this->priority > 0) {
			$count = count(array_diff(array_keys($controller->viewVars), array('title_for_layout')));
			$this->title = __dn('debug_kit', '<b>%d</b> var', '<b>%d</b> vars', $count, $count);
		}

		return array_merge($controller->viewVars, array('$request->data' => $controller->request->data));
	}

}
