<?php
/**
 * DebugKit TimedBehavior
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
 * @subpackage    debug_kit.models.behaviors
 * @since         DebugKit 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('DebugKitDebugger', 'DebugKit.Lib');

class TimedBehavior extends ModelBehavior {

/**
 * Behavior settings
 *
 * @var array
 */
	public $settings = array();

/**
 * Default setting values
 *
 * @var array
 */
	protected $_defaults = array();

/**
 * Setup the behavior and import required classes.
 *
 * @param object $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @return void
 */
	public function setup(Model $Model, $settings = null) {
		if (is_array($settings)) {
			$this->settings[$Model->alias] = array_merge($this->_defaults, $settings);
		} else {
			$this->settings[$Model->alias] = $this->_defaults;
		}
	}

/**
 * beforeFind, starts a timer for a find operation.
 *
 * @param Model $Model
 * @param array $queryData Array of query data (not modified)
 * @return boolean true
 */
	public function beforeFind(Model $Model, $queryData) {
		DebugKitDebugger::startTimer($Model->alias . '_find', $Model->alias . '->find()');
		return true;
	}

/**
 * afterFind, stops a timer for a find operation.
 *
 * @param Model $Model
 * @param array $results Array of results
 * @return boolean true.
 */
	public function afterFind(Model $Model, $results, $primary) {
		DebugKitDebugger::stopTimer($Model->alias . '_find');
		return true;
	}

/**
 * beforeSave, starts a time before a save is initiated.
 *
 * @param Model $Model
 * @param array $options
 * @return boolean true
 */
	public function beforeSave(Model $Model, $options = array()) {
		DebugKitDebugger::startTimer($Model->alias . '_save', $Model->alias . '->save()');
		return true;
	}

/**
 * afterSave, stop the timer started from a save.
 *
 * @param string $Model
 * @param string $created
 * @return void
 */
	public function afterSave(Model $Model, $created) {
		DebugKitDebugger::stopTimer($Model->alias . '_save');
		return true;
	}
}
