<?php
/**
 * Debug Panel
 *
 * Base class for debug panels.
 *
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class DebugPanel {

/**
 * Defines which plugin this panel is from so the element can be located.
 *
 * @var string
 */
	public $plugin = 'DebugKit';

/**
 * Defines the title for displaying on the toolbar. If null, the class name will be used.
 * Overriding this allows you to define a custom name in the toolbar.
 *
 * @var string
 */
	public $title = null;

/**
 * Panel's css files
 *
 * @var array
 */
	public $css = array();

/**
 * Panel's javascript files
 *
 * @var array
 */
	public $javascript = array();

/**
 * Provide a custom element name for this panel. If null, the underscored version of the class
 * name will be used.
 *
 * @var string
 */
	public $elementName = null;

	public function __construct() {
	}

/**
 * startup the panel
 *
 * Pull information from the controller / request
 *
 * @param object $controller Controller reference.
 * @return void
 */
	public function startup(Controller $controller) {
	}

/**
 * Prepare output vars before Controller Rendering.
 *
 * @param object $controller Controller reference.
 * @return void
 */
	public function beforeRender(Controller $controller) {
	}

}
