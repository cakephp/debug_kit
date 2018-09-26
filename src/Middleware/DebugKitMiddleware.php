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

use Cake\Core\Configure;
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
     * @param DebugKit\ToolbarService $service The configured service, or null.
     */
    public function __construct(ToolbarService $service = null)
    {
        $service = $service ?: new ToolbarService(EventManager::instance(), (array)Configure::read('DebugKit'));
        $this->service = $service;
    }

    /**
     * Invoke the middleware.
     *
     * DebugKit will augment the response and add the toolbar if possible.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Psr\Http\Message\ResponseInterface A response
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
