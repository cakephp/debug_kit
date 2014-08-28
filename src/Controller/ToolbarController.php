<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Error\NotFoundException;
use Cake\Event\Event;

/**
 * Provides utility features need by the toolbar.
 */
class ToolbarController extends Controller {

/**
 * components
 *
 * @var array
 */
	public $components = ['RequestHandler'];

/**
 * Before filter handler.
 *
 * @param \Cake\Event\Event $event The event.
 * @return void
 * @throws \Cake\Error\NotFoundException
 */
	public function beforeFilter(Event $event) {
		// TODO add config override.
		if (!Configure::read('debug')) {
			throw new NotFoundException();
		}
	}

/**
 * Clear a named cache.
 *
 */
	public function clear_cache($name) {
		$result = Cache::clear($name, false);
		$this->set([
			'_serialize' => ['success'],
			'success' => $result,
		]);
	}

}
