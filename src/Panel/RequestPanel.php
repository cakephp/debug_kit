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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\DebugKit\Panel;

use Cake\Controller\Controller;
use Cake\Routing\Router;
use Cake\DebugKit\DebugPanel;

/**
 * Provides debug information on the Current request params.
 *
 */
class RequestPanel extends DebugPanel {

/**
 * beforeRender callback - grabs request params
 *
 * @param Controller $controller
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$out = [];
		$out['params'] = $controller->request->params;
		$out['query'] = $controller->request->query;
		$out['data'] = $controller->request->data;
		$out['cookie'] = $controller->request->cookies;
		$out['get'] = $_GET;
		return $out;
	}
}
