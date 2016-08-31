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
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Routing\DispatcherFactory;
use DebugKit\Routing\Filter\DebugBarFilter;

$debugBar = new DebugBarFilter(EventManager::instance(), (array)Configure::read('DebugKit'));

if (!$debugBar->isEnabled() || php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg') {
    return;
}

$hasDebugKitConfig = ConnectionManager::config('debug_kit');
if (!$hasDebugKitConfig && !in_array('sqlite', PDO::getAvailableDrivers())) {
    $msg = 'DebugKit not enabled. You need to either install pdo_sqlite, ' .
        'or define the "debug_kit" connection name.';
    Log::warning($msg);
    return;
}

if (!$hasDebugKitConfig) {
    ConnectionManager::config('debug_kit', [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Sqlite',
        'database' => TMP . 'debug_kit.sqlite',
        'encoding' => 'utf8',
        'cacheMetadata' => true,
        'quoteIdentifiers' => false,
    ]);
}

if (Plugin::routes('DebugKit') === false) {
    require __DIR__ . DS . 'routes.php';
}

// Setup toolbar
$debugBar->setup();
DispatcherFactory::add($debugBar);
