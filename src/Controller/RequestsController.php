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
namespace Cake\DebugKit\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Error\NotFoundException;
use Cake\Event\Event;

/**
 * Provides access to panel data.
 */
class RequestsController extends Controller {

	public $layout = 'DebugKit.toolbar';
/**
 * Before filter handler.
 *
 * @param \Cake\Event\Event $event The event.
 * @return void
 * @throws \Cake\Error\NotFoundException
 */
	public function beforeFilter(Event $event) {
		// TODO add config override
		if (!Configure::read('debug')) {
			throw new NotFoundException();
		}
	}

/**
 * Get a paginated list of requests.
 *
 * @return void
 */
	public function index() {
		$this->paginate = ['contain' => 'Panels'];
		$toolbars = $this->paginate($this->Requests);
		$this->set('toolbar', $toolbars);
	}

/**
 * View a request's data.
 *
 * @param string $id The id.
 * @return void
 */
	public function view($id = null) {
		$toolbar = $this->Requests->get($id, ['contain' => 'Panels']);
		$this->set('toolbar', $toolbar);
	}
}
