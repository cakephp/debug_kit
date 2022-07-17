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
namespace DebugKit\Panel;

use Cake\Event\EventInterface;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the Session contents.
 */
class SessionPanel extends DebugPanel
{
    /**
     * shutdown callback
     *
     * @param \Cake\Event\EventInterface $event The event
     * @return void
     */
    public function shutdown(EventInterface $event)
    {
        /** @var \Cake\Http\ServerRequest|null $request */
        $request = $event->getSubject()->getRequest();
        if ($request) {
            $this->_data = ['content' => $request->getSession()->read()];
        }
    }
}
