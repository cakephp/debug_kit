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
 * Provides access to panel data.
 *
 * @property \DebugKit\Model\Table\RequestsTable $Requests
 */
class RequestsController extends DebugKitController
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
        $this->response->header(['Content-Security-Policy' => '']);

        parent::beforeFilter($event);
    }

    /**
     * Before render handler.
     *
     * @param \Cake\Event\Event $event The event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $this->response->type('html');
        $this->viewBuilder()
            ->layout('DebugKit.toolbar')
            ->className('DebugKit.Ajax');

        parent::beforeRender($event);
    }

    /**
     * View a request's data.
     *
     * @param string $id The id.
     * @return void
     */
    public function view($id = null)
    {
        $toolbar = $this->Requests->get($id, ['contain' => 'Panels']);
        $this->set('toolbar', $toolbar);
    }
}
