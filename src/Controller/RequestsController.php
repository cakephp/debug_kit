<?php
declare(strict_types=1);

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

use Cake\Event\EventInterface;

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
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->response = $this->response->withHeader('Content-Security-Policy', '');
    }

    /**
     * Before render handler.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     */
    public function beforeRender(EventInterface $event)
    {
        $this->viewBuilder()
            ->setLayout('DebugKit.toolbar')
            ->setClassName('DebugKit.Ajax');
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
