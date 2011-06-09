<?php

/**
 * Request Panel
 *
 * Provides debug information on the Current request params.
 *
 * @package       cake.debug_kit.panels
 **/
class RequestPanel extends DebugPanel {

	public $plugin = 'debug_kit';

/**
 * beforeRender callback - grabs request params
 *
 * @return array
 **/
	public function beforeRender($controller) {
		$out = array();
		$out['params'] = $controller->request->params;
		$out['url'] = $controller->request->url;
		$out['query'] = $controller->request->query;
		if (isset($controller->Cookie)) {
			$out['cookie'] = $controller->Cookie->read();
		}
		$out['get'] = $_GET;
		$out['currentRoute'] = Router::currentRoute();
		return $out;
	}
}
