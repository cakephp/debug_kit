<?php
App::uses('DebugPanel', 'DebugKit.Lib');
/**
 * History Panel
 *
 * Provides debug information on previous requests.
 *
 * @package       cake.debug_kit.panels
 **/
class HistoryPanel extends DebugPanel {

/**
 * Number of history elements to keep
 *
 * @var string
 **/
	public $history = 5;

/**
 * Constructor
 *
 * @param array $settings Array of settings.
 * @return void
 **/
	public function __construct($settings) {
		if (isset($settings['history'])) {
			$this->history = $settings['history'];
		}
	}

/**
 * beforeRender callback function
 *
 * @return array contents for panel
 **/
	public function beforeRender(Controller $controller) {
		$cacheKey = $controller->Toolbar->cacheKey;
		$toolbarHistory = Cache::read($cacheKey, 'debug_kit');
		$historyStates = array();
		if (is_array($toolbarHistory) && !empty($toolbarHistory)) {
			$prefix = array();
			if (!empty($controller->request->params['prefix'])) {
				$prefix[$controller->request->params['prefix']] = false;
			}
			foreach ($toolbarHistory as $i => $state) {
				if (!isset($state['request']['content']['url'])) {
					continue;
				}
				$title = $state['request']['content']['url'];
				$query = @$state['request']['content']['query'];
				if (isset($query['url'])) {
					unset($query['url']);
				}
				if (!empty($query)) {
					$title .= '?' . urldecode(http_build_query($query));
				}
				$historyStates[] = array(
					'title' => $title,
					'url' => array_merge($prefix, array(
						'plugin' => 'debug_kit',
						'controller' => 'toolbar_access',
						'action' => 'history_state',
						$i + 1))
				);
			}
		}
		if (count($historyStates) >= $this->history) {
			array_pop($historyStates);
		}
		return $historyStates;
	}
}
