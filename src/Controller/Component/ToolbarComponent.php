<?php
/**
 * DebugKit DebugToolbar Component
 *
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
namespace DebugKit\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Class ToolbarComponent
 *
 * @since         DebugKit 0.1
 */
class ToolbarComponent extends Component {

/**
 * Constructor
 *
 * If debug is off the component will be disabled and not do any further time tracking
 * or load the toolbar helper.
 *
 * @param ComponentCollection $collection
 * @param array $settings
 * @return void
 */
	public function __construct(ComponentRegistry $collection, $settings = array()) {
		$msg = 'DebugKit is now loaded through plugin bootstrapping. Make sure you have ' .
			'`Plugin::load("DebugKit", ["bootstrap" => true]);` in your application\'s bootstrap.php.';
		throw new \RuntimeException($msg);
	}
}
