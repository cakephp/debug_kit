<?php
App::uses('DebugPanel', 'DebugKit.Lib');

/**
 * Request Panel
 *
 * Provides debug information on the Current request params.
 *
 * @package       cake.debug_kit.panels
 **/
class RequestPanel extends DebugPanel {

/**
 * beforeRender callback - grabs request params
 *
 * @return array
 **/
	public function beforeRender(Controller $controller) {
		$out = array();
		$out['params'] = $controller->request->params;
		$out['url'] = $controller->request->url;
		$out['query'] = $controller->request->query;
		$out['data'] = $controller->request->data;
		if (isset($controller->Cookie)) {
			$out['cookie'] = $controller->Cookie->read();
		}
		$out['get'] = $_GET;
		$out['currentRoute'] = Router::currentRoute();
		return $out;
	}
}
