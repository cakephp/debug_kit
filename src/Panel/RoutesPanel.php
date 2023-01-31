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
     * @return string
     */
    public function summary()
    {
        $appClass = Configure::read('App.namespace') . '\Application';
        if (class_exists($appClass, false) && !Router::$initialized) {
            return '0';
        }

        $routes = array_filter(Router::routes(), function ($route) {
            return (!isset($routes->defaults['plugin'])) || $route->defaults['plugin'] !== 'DebugKit';
        });

        return (string)count($routes);
    }

    /**
     * Data collection callback.
     *
     * @param \Cake\Event\Event $event The shutdown event.
     * @return void
     */
    public function shutdown(Event $event)
    {
        /* @var \Cake\Controller\Controller|null $controller */
        $controller = $event->getSubject();
        $request = $controller ? $controller->getRequest() : null;
        $this->_data = [
            'matchedRoute' => $request ? $request->getParam('_matchedRoute') : null,
        ];
    }
}
