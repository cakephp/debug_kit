<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Panel;

use Cake\Event\Event;
use Cake\Http\ServerRequest as Request;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the Session contents.
 */
class SessionPanel extends DebugPanel
{

    /**
     * shutdown callback
     *
     * @param \Cake\Event\Event $event The event
     * @return void
     */
    public function shutdown(Event $event)
    {
        /* @var Request $request */
        $request = $event->getSubject()->request;
        if ($request) {
            $this->_data = ['content' => $request->getSession()->read()];
        }
    }
}
