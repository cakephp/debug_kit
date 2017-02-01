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
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

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

$dumper = new HtmlDumper();
$dumper->setStyles([
    'default' => 'background-color:#ededed; color:#a0a0a0; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: normal',
    'num' => 'font-weight:bold; color:#2a6496',
    'const' => 'font-weight:bold; color:#9c27b0',
    'str' => 'font-weight:bold; color:#26a69a',
    'note' => 'color:#d33c44',
    'ref' => 'color:#a0a0a0',
    'public' => 'color:#2a6496',
    'protected' => 'color:#2a6496',
    'private' => 'color:#2a6496',
    'meta' => 'color:#2a6496',
    'key' => 'color:#2a6496',
    'index' => 'color:#2a6496',
]);
$cloner = new VarCloner();
VarDumper::setHandler(function ($var) use ($cloner, $dumper) {
    $dumper->dump($cloner->cloneVar($var));
});
