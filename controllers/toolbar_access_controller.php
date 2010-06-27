<?php
/**
 * DebugKit ToolbarAccess Controller
 *
 * Allows retrieval of information from the debugKit internals.
 *
 * PHP versions 4 and 5
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
class ToolbarAccessController extends DebugKitAppController {
/**
 * name
 *
 * @var string
 */
	var $name = 'ToolbarAccess';

/**
 * Helpers
 *
 * @var array
 **/
	var $helpers = array(
		'DebugKit.Toolbar' => array('output' => 'DebugKit.HtmlToolbar'),
		'Javascript', 'Number', 'DebugKit.SimpleGraph'
	);

/**
 * Components
 *
 * @var array
 **/
	var $components = array('RequestHandler', 'DebugKit.Toolbar');

/**
 * Uses
 *
 * @var array
 **/
	var $uses = array('DebugKit.ToolbarAccess');

/**
 * beforeFilter callback
 *
 * @return void
 **/
	function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->Toolbar)) {
			$this->Toolbar->enabled = false;
		}
		$this->helpers['DebugKit.Toolbar']['cacheKey'] = $this->Toolbar->cacheKey;
		$this->helpers['DebugKit.Toolbar']['cacheConfig'] = 'debug_kit';
	}

/**
 * Get a stored history state from the toolbar cache.
 *
 * @return void
 **/
	function history_state($key = null) {
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
	function sql_explain() {
		if (
			!$this->RequestHandler->isPost() ||
			empty($this->data['log']['sql']) || 
			empty($this->data['log']['ds']) ||
			empty($this->data['log']['hash']) ||
			Configure::read('debug') == 0
		) {
			$this->cakeError('error404', array(array(
				'message' => 'Invalid parameters'
			)));
		}
		App::import('Core', 'Security');
		$hash = Security::hash($this->data['log']['sql'] . $this->data['log']['ds'], null, true);
		if ($hash !== $this->data['log']['hash']) {
			$this->cakeError('error404', array(array(
				'message' => 'Invalid parameters'
			)));
		}
		$result = $this->ToolbarAccess->explainQuery($this->data['log']['ds'], $this->data['log']['sql']);
		$this->set(compact('result'));
	}
}