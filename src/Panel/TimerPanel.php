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
use DebugKit\DebugPanel;
use DebugKit\DebugMemory;
use DebugKit\DebugTimer;

/**
 * Provides debug information on all timers used in a request.
 */
class TimerPanel extends DebugPanel {

/**
 * Return an array of events to listen to.
 *
 * @return array
 */
	public function implementedEvents() {
		$before = function ($name) {
			return function () use ($name) {
				DebugTimer::start($name, __d('debug_kit', $name));
			};
		};
		$after = function ($name) {
			return function () use ($name) {
				DebugTimer::stop($name);
			};
		};

		return array(
			'Controller.initialize' => array(
				array('priority' => 0, 'callable' => function() {
					DebugMemory::record(__d('debug_kit', 'Controller initialization'));
				}),
				array('priority' => 0, 'callable' => $before('Event: Controller.initialize')),
				array('priority' => 999, 'callable' => $after('Event: Controller.initialize'))
			),
			'Controller.startup' => array(
				array('priority' => 0, 'callable' => $before('Event: Controller.startup')),
				array('priority' => 999, 'callable' => $after('Event: Controller.startup')),
				array('priority' => 999, 'callable' => function() {
					DebugMemory::record(__d('debug_kit', 'Controller action start'));
					DebugTimer::start(__d('debug_kit', 'Controller action'));
				}),
			),
			'Controller.beforeRender' => array(
				array('priority' => 0, 'callable' => function() {
					DebugTimer::stop(__d('debug_kit', 'Controller action'));
				}),
				array('priority' => 0, 'callable' => $before('Event: Controller.beforeRender')),
				array('priority' => 999, 'callable' => $after('Event: Controller.beforeRender')),
				array('priority' => 999, 'callable' => function() {
					DebugMemory::record(__d('debug_kit', 'View Render start'));
					DebugTimer::start(__d('debug_kit', 'View Render start'));
				}),
			),
			'Controller.beforeRedirect' => 'beforeRedirect',
			'View.beforeRender' => array(
				array('priority' => 0, 'callable' => $before('Event: View.beforeRender')),
				array('priority' => 999, 'callable' => $after('Event: View.beforeRender'))
			),
			'View.afterRender' => array(
				array('priority' => 0, 'callable' => $before('Event: View.afterRender')),
				array('priority' => 999, 'callable' => $after('Event: View.afterRender'))
			),
			'View.beforeLayout' => array(
				array('priority' => 0, 'callable' => $before('Event: View.beforeLayout')),
				array('priority' => 999, 'callable' => $after('Event: View.beforeLayout'))
			),
			'View.afterLayout' => array(
				array('priority' => 0, 'callable' => $before('Event: View.afterLayout')),
				array('priority' => 999, 'callable' => $after('Event: View.afterLayout'))
			),
			'View.beforeRenderFile' => array(
				array('priority' => 0, 'callable' => function($event, $filename) {
					DebugTimer::start(__d('debug_kit', 'Render {0}', $filename));
				}),
			),
			'View.afterRenderFile' => array(
				array('priority' => 0, 'callable' => function($event, $filename) {
					DebugTimer::stop(__d('debug_kit', 'Render {0}', $filename));
				}),
			),
			'Controller.shutdown' => array(
				array('priority' => 0, 'callable' => $before('Event: Controller.shutdown')),
				array('priority' => 0, 'callable' => function() {
					DebugTimer::stop(__d('debug_kit', 'View Render start'));
					DebugMemory::record(__d('debug_kit', 'Controller shutdown'));
				}),
				array('priority' => 999, 'callable' => $after('Event: Controller.shutdown')),
			),
		);
	}

/**
 * Get the data for the panel.
 *
 * @return array
 */
	public function data() {
		return [
			'requestTime' => DebugTimer::requestTime(),
			'timers' => DebugTimer::getAll(),
			'memory' => DebugMemory::getAll(),
			'peakMemory' => DebugMemory::getPeak(),
		];
	}

}
