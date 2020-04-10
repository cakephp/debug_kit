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
        $routes = array_filter(Router::routes(), function ($route) {
            return !isset($route->defaults['plugin']) || $route->defaults['plugin'] !== 'DebugKit';
        });

        return (string)count($routes);
    }

    /**
     * Data collection callback.
     *
     * @param \Cake\Event\EventInterface $event The shutdown event.
     * @return void
     */
    public function shutdown(EventInterface $event)
    {
        /** @var \Cake\Controller\Controller|null $controller */
        $controller = $event->getSubject();
        $request = $controller ? $controller->getRequest() : null;
        $this->_data = [
            'matchedRoute' => $request ? $request->getParam('_matchedRoute') : null,
        ];
    }
}
