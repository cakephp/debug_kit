<?php
/**
 * Debug View
 *
 * Custom Debug View class, helps with development.
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::uses('ToolbarComponent', 'DebugKit.Controller/Component');
App::uses('DebugKitDebugger', 'DebugKit.vendors');

/**
 * DebugView used by DebugKit
 *
 * @package debug_kit.views
 */
class DebugView extends DoppelGangerView {
/**
 * Overload _render to capture filenames and time actual rendering of each view file
 *
 * @param string $___viewFn Filename of the view
 * @param array $___dataForView Data to include in rendered view
 * @return string Rendered output
 */
	protected function _render($___viewFn, $___dataForView = array()) {
		if (!isset($___dataForView['disableTimer'])) {
			DebugKitDebugger::startTimer('render_' . basename($___viewFn), __d('debug_kit', 'Rendering %s', Debugger::trimPath($___viewFn)));
		}
		$out = parent::_render($___viewFn, $___dataForView);

		if (!isset($___dataForView['disableTimer'])) {
			DebugKitDebugger::stopTimer('render_' . basename($___viewFn));
		}
		return $out;
	}

/**
 * Element method, adds comment injection to the features View offers.
 *
 * @return void
 */
	public function element($name, $data = array(), $options = array()) {
		$out = '';
		$isHtml = (
			(isset($this->request->params['url']['ext']) && $this->request->params['url']['ext'] === 'html') ||
			!isset($this->request->params['url']['ext'])
		);
		if ($isHtml) {
			$out .= sprintf("<!-- %s - %s -->\n", __d('debug_kit', 'Starting to render'), $name); 
		}

		$out .= parent::element($name, $data, $options);

		if ($isHtml) {
			$out .= sprintf("\n<!-- %s - %s -->\n", __d('debug_kit', 'Finished'), $name);
		}
		return $out;
	}

/**
 * Renders view for given action and layout.
 * Adds timers, for all subsequent rendering, and injects the debugKit toolbar.
 *
 * @param string $action Name of action to render for
 * @param string $layout Layout to use
 * @return string Rendered Element
 */
	public function render($action = null, $layout = null) {
		DebugKitDebugger::startTimer('viewRender', __d('debug_kit', 'Rendering View'));

		$out = parent::render($action, $layout);

		DebugKitDebugger::stopTimer('viewRender');
		DebugKitDebugger::stopTimer('controllerRender');
		DebugKitDebugger::setMemoryPoint(__d('debug_kit', 'View render complete'));

		if (empty($this->request->params['requested']) && $this->Helpers->attached('Toolbar')) {
			$backend = $this->Helpers->Toolbar->getName();
			$this->Helpers->Toolbar->{$backend}->send();
		}
		if (empty($this->output)) {
			return $out;
		}
		return $this->output;
	}
}
