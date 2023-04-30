<?php
declare(strict_types=1);

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

use Cake\Error\Debugger;
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
    public function shutdown(EventInterface $event): void
    {
        /** @var \Cake\Http\ServerRequest|null $request */
        $request = $event->getSubject()->getRequest();
        if ($request) {
            $content = Debugger::exportVarAsNodes($request->getSession()->read());
            $this->_data = compact('content');
        }
    }
}
