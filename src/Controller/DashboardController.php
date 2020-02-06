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

use Cake\Event\Event;

/**
 * Dashboard and common DebugKit backend.
 */
class DashboardController extends DebugKitController
{
    /**
     * Before filter handler.
     *
     * @param \Cake\Event\Event $event The event.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('dashboard');
    }

    /**
     * Dashboard.
     *
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function index()
    {
        $this->loadModel('DebugKit.Requests');

        $data = [
            'driver' => get_class($this->Requests->getConnection()->getDriver()),
            'rows' => $this->Requests->find()->count(),
        ];

        $this->set('connection', $data);
    }

    /**
     * Reset SQLite DB.
     *
     * @return \Cake\Http\Response
     */
    public function reset()
    {
        $this->request->allowMethod('post');
        $this->loadModel('DebugKit.Requests');

        $this->Requests->Panels->deleteAll('1=1');
        $this->Requests->deleteAll('1=1');

        return $this->redirect(['action' => 'index']);
    }
}
