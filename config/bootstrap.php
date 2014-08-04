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
use Cake\Datasource\ConnectionManager;

ConnectionManager::create('debug_kit', [
	'className' => 'Cake\Database\Connection',
	'driver' => 'Cake\Database\Driver\Sqlite',
	'database' => TMP . 'debug_kit.sqlite',
	'encoding' => 'utf8',
	'cacheMetadata' => true,
	'quoteIdentifiers' => false,
]);
