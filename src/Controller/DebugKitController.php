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
namespace DebugKit\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

/**
 * Dashboard and common DebugKit backend.
 */
class DebugKitController extends Controller
{
    /**
     * Before filter handler.
     *
     * @param \Cake\Event\Event $event The event.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function beforeFilter(Event $event)
    {
        // TODO add config override.
        if (!Configure::read('debug')) {
            throw new NotFoundException();
        }
    }

    /**
     * Dashboard.
     *
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function index()
    {
        $connection = ConnectionManager::getConfig('debug_kit');

        if ($connection['driver'] === Sqlite::class) {
            $connection['location'] = str_replace(ROOT . DS, DS, $connection['database']);
            $connection['size'] = 0;
            if (file_exists($connection['database'])) {
                $connection['size'] = filesize($connection['database']);
            }
        }

        $this->set(compact('connection'));
    }

    /**
     * Reset SQLite DB.
     *
     * @return \Cake\Http\Response|null
     */
    public function reset()
    {
        $this->request->allowMethod('post');

        $connection = ConnectionManager::getConfig('debug_kit');
        if ($connection['driver'] === Sqlite::class && file_exists($connection['database'])) {
            unlink($connection['database']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
