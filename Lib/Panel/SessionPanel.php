<?php
/**
 * Session Panel
 *
 * Provides debug information on the Session contents.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       DebugKit.Lib.Panel
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Class SessionPanel
 *
 * @package       DebugKit.Lib.Panel
 */
class SessionPanel extends DebugPanel {

/**
 * beforeRender callback
 *
 * @param \Controller|object $controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$sessions = $controller->Toolbar->Session->read();
		return $sessions;
	}
}
