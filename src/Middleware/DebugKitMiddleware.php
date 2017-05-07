<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Middleware;

use Cake\Event\EventManager;
use DebugKit\ToolbarService;

/**
 * PSR-7 Middleware that enables DebugKit for the layers below.
 */
class DebugKitMiddleware
{
    /**
     * @var \DebugKit\ToolbarService
     */
    protected $service;

    /**
     * Constructor
     *
     * @param array $config The configuration data for DebugKit.
     */
    public function __construct(array $config = [])
    {
        $events = EventManager::instance();
        $this->service = new ToolbarService($events, $config);
    }

    /**
     * Invoke the middleware.
     *
     * DebugKit will augment the response and add the toolbar if possible.
     */
    public function __invoke($request, $response, $next)
    {
        $this->service->loadPanels();
        $this->service->initializePanels();
        $response = $next($request, $response);
        $row = $this->service->saveData($request, $response);
        if (!$row) {
            return $response;
        }

        return $this->service->injectScripts($row, $response);
    }
}
