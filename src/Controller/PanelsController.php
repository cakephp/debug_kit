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

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Error\NotFoundException;
use Cake\Event\Event;

/**
 * Provides access to panel data.
 */
class PanelsController extends Controller {

	public $layout = 'DebugKit.panel';

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
 * View a panel's data.
 *
 * @param string $id The id.
 */
	public function view($id = null) {
		$panel = $this->Panels->get($id);
		$this->set('panel', $panel);
		$this->set(unserialize($panel->content));
	}
}
