<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;
use DebugKit\Routing\Filter\DebugBarFilter;

$debugBar = new DebugBarFilter(EventManager::instance(), (array)Configure::read('DebugKit'));

if (!$debugBar->isEnabled() || php_sapi_name() === 'cli') {
	return;
}

if (!ConnectionManager::config('debug_kit')) {
	ConnectionManager::config('debug_kit', [
		'className' => 'Cake\Database\Connection',
		'driver' => 'Cake\Database\Driver\Sqlite',
		'database' => TMP . 'debug_kit.sqlite',
		'encoding' => 'utf8',
		'cacheMetadata' => true,
		'quoteIdentifiers' => false,
	]);
}

Router::plugin('DebugKit', function($routes) {
	$routes->extensions('json');
	$routes->connect(
		'/toolbar/clear_cache',
		['controller' => 'Toolbar', 'action' => 'clearCache']
	);
	$routes->connect(
		'/toolbar/*',
		['controller' => 'Requests', 'action' => 'view']
	);
	$routes->connect(
		'/panels/view/*',
		['controller' => 'Panels', 'action' => 'view']
	);
	$routes->connect(
		'/panels/*',
		['controller' => 'Panels', 'action' => 'index']
	);
});


// Setup toolbar
$debugBar->setup();
DispatcherFactory::add($debugBar);
