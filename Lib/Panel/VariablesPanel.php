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

/**
 * beforeRender callback
 *
 * @return array
 */
	public function beforeRender(Controller $controller) {
		return array_merge($controller->viewVars, array('$request->data' => $controller->request->data));
	}

}
