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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\DebugKit;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Event\EventListener;
use Cake\Utility\Inflector;

/**
 * Base class for debug panels.
 *
 * @since         DebugKit 0.1
 */
class DebugPanel implements EventListener {

/**
 * Defines which plugin this panel is from so the element can be located.
 *
 * @var string
 */
	public $plugin = 'DebugKit';

/**
 * Title attribute
 *
 * @deprecated
 */
	public $title;

/**
 * The data collected about a given request.
 *
 * @var array
 */
	protected $_data = [];

/**
 * Get the panel name.
 *
 * Inflected from the classname by default. Changing this will update
 * both the element and title.
 *
 * @return string
 */
	protected function _name() {
		list($ns, $name) = namespaceSplit(get_class($this));
		return substr($name, 0, strlen('Panel') * -1);
	}
/**
 * Get the title for the panel.
 *
 * @return string
 */
	public function title() {
		$name = $this->_name();
		return Inflector::humanize(Inflector::underscore($name));
	}

/**
 * Get the element name for the panel.
 *
 * @return string
 */
	public function elementName() {
		$name = $this->_name();
		return $this->plugin . '.' . Inflector::underscore($name);
	}

/**
 * startup the panel
 *
 * Pull information from the controller / request
 *
 * Old style callbacks for non-event based toolbar.
 *
 * @param \Cake\Controller\Controller $controller controller reference.
 * @return void
 */
	public function startup(Controller $event) {
	}

/**
 * Prepare output vars before Controller Rendering.
 *
 * Old style callbacks for non-event based toolbar.
 *
 * @param \Cake\Controller\Controller $controller controller reference.
 * @return void
 */
	public function beforeRender(Controller $event) {
	}

/**
 * Get the data a panel has collected.
 *
 * @return array
 */
	public function data() {
		return $this->_data;
	}

/**
 * Initialize callback
 *
 * @param \Cake\Event\Event $event The event.
 * @return void
 */
	public function initialize(Event $event) {
	}

/**
 * Shutdown callback
 *
 * @param \Cake\Event\Event $event The event.
 * @return void
 */
	public function shutdown(Event $event) {
	}

/**
 * Get the events this panels supports.
 *
 * @return array
 */
	public function implementedEvents() {
		return [
			'Controller.initialize' => 'initialize',
			'Controller.shutdown' => 'shutdown',
		];
	}
}
