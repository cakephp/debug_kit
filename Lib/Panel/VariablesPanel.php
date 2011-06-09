<?php

/**
 * Variables Panel
 *
 * Provides debug information on the View variables.
 *
 * @package       cake.debug_kit.panels
 **/
class VariablesPanel extends DebugPanel {

	public $plugin = 'debug_kit';

/**
 * beforeRender callback
 *
 * @return array
 **/
	public function beforeRender($controller) {
		return array_merge($controller->viewVars, array('$request->data' => $controller->request->data));
	}
}
