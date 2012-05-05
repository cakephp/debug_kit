<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Timer Panel
 *
 * Provides debug information on all timers used in a request.
 *
 * @package       cake.debug_kit.panels
 */
class TimerPanel extends DebugPanel {

/**
 * startup - add in necessary helpers
 *
 * @return void
 */
	public function startup(Controller $controller) {
		if (!in_array('Number', array_keys(HelperCollection::normalizeObjectArray($controller->helpers)))) {
			$controller->helpers[] = 'Number';
		}
		if (!in_array('SimpleGraph', array_keys(HelperCollection::normalizeObjectArray($controller->helpers)))) {
			$controller->helpers[] = 'DebugKit.SimpleGraph';
		}
	}
}
