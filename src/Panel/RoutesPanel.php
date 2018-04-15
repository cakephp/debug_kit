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
 *
 */
namespace DebugKit\Panel;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use DebugKit\DebugPanel;

/**
 * A panel to get the list of connected routes for the application
 */
class RoutesPanel extends DebugPanel
{

    /**
     * Get summary data for the routes panel.
     *
     * @return int
     */
    public function summary()
    {
        $appClass = Configure::read('App.namespace') . '\Application';
        if (class_exists($appClass, false) && !Router::$initialized) {
            return 0;
        }

        return count(Router::routes());
    }

    /**
     * Data collection callback.
     *
     * @param \Cake\Event\Event $event The shutdown event.
     * @return void
     */
    public function shutdown(Event $event)
    {
        $controller = $event->getSubject();
        /* @var \Cake\Http\ServerRequest $request */
        $request = $controller ? $controller->request : null;
        $this->_data = [
            'matchedRoute' => $request ? $request->getParam('_matchedRoute') : null,
        ];
    }
}
