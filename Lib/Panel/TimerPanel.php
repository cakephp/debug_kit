<?php

/**
 * Timer Panel
 *
 * Provides debug information on all timers used in a request.
 *
 * @package       cake.debug_kit.panels
 **/
class TimerPanel extends DebugPanel {

	public $plugin = 'debug_kit';
	
/**
 * startup - add in necessary helpers
 *
 * @return void
 **/
	public function startup($controller) {
		if (!in_array('Number', $controller->helpers)) {
			$controller->helpers[] = 'Number';
		}
		if (!in_array('SimpleGraph', $controller->helpers)) {
			$controller->helpers[] = 'DebugKit.SimpleGraph';
		}
	}
}
