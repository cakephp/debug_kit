<?php
/**
 * DebugKit ToolbarAccess Controller
 *
 * Allows retrieval of information from the debugKit internals.
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
 * @subpackage    debug_kit.controllers
 * @since         DebugKit 1.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::uses('Security', 'Utility');
App::uses('DebugKitAppController', 'DebugKit.Controller');

class ToolbarAccessController extends DebugKitAppController {
/**
 * name
 *
 * @var string
 */
	public $name = 'ToolbarAccess';

/**
 * Helpers
 *
 * @var array
 **/
	public $helpers = array(
		'DebugKit.Toolbar' => array('output' => 'DebugKit.HtmlToolbar'),
		'Js', 'Number', 'DebugKit.SimpleGraph'
	);

/**
 * Components
 *
 * @var array
 **/
	public $components = array('RequestHandler', 'DebugKit.Toolbar');

/**
 * Uses
 *
 * @var array
 **/
	public $uses = array('DebugKit.ToolbarAccess');

/**
 * beforeFilter callback
 *
 * @return void
 **/
	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->Toolbar)) {
			$this->Components->disable('Toolbar');
		}
		$this->helpers['DebugKit.Toolbar']['cacheKey'] = $this->Toolbar->cacheKey;
		$this->helpers['DebugKit.Toolbar']['cacheConfig'] = 'debug_kit';
	}

/**
 * Get a stored history state from the toolbar cache.
 *
 * @return void
 **/
	public function history_state($key = null) {
		if (Configure::read('debug') == 0) {
			return $this->redirect($this->referer());
		}
		$oldState = $this->Toolbar->loadState($key);
		$this->set('toolbarState', $oldState);
		$this->set('debugKitInHistoryMode', true);
	}

/**
 * Run SQL explain/profiling on queries. Checks the hash + the hashed queries, 
 * if there is mismatch a 404 will be rendered.  If debug == 0 a 404 will also be
 * rendered.  No explain will be run if a 404 is made.
 *
 * @return void
 */
	public function sql_explain() {
		if (
			!$this->request->is('post') ||
			empty($this->request->data['log']['sql']) || 
			empty($this->request->data['log']['ds']) ||
			empty($this->request->data['log']['hash']) ||
			Configure::read('debug') == 0
		) {
			throw new BadRequestException('Invalid parameters');
		}
		$hash = Security::hash($this->request->data['log']['sql'] . $this->request->data['log']['ds'], null, true);
		if ($hash !== $this->request->data['log']['hash']) {
			throw new BadRequestException('Invalid parameters');
		}
		$result = $this->ToolbarAccess->explainQuery($this->request->data['log']['ds'], $this->request->data['log']['sql']);
		$this->set(compact('result'));
	}
}