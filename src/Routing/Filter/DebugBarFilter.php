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
namespace DebugKit\Routing\Filter;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\DispatcherFilter;
use DebugKit\ToolbarService;

/**
 * Toolbar injector filter.
 *
 * This class loads all the panels into the registry
 * and binds the correct events into the provided event
 * manager
 *
 * @deprecated Dispatch filters are deprecated. Long term this filter
 * will be removed and replaced with middleware.
 */
class DebugBarFilter extends DispatcherFilter
{
    /**
     * @var \DebugKit\ToolbarService
     */
    protected $service;

    /**
     * Constructor
     *
     * @param \Cake\Event\EventManager $events The event manager to use.
     * @param array $config The configuration data for DebugKit.
     */
    public function __construct(EventManager $events, array $config)
    {
        parent::__construct($config);

        $this->service = new ToolbarService($events, $config);
    }

    /**
     * Event bindings
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Dispatcher.beforeDispatch' => [
                'callable' => 'beforeDispatch',
                'priority' => 0,
            ],
            'Dispatcher.afterDispatch' => [
                'callable' => 'afterDispatch',
                'priority' => 9999,
            ],
        ];
    }

    /**
     * Check whether or not debug kit is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->service->isEnabled();
    }

    /**
     * Get the list of loaded panels
     *
     * @return array
     */
    public function loadedPanels()
    {
        return $this->service->registry()->loaded();
    }

    /**
     * Get the list of loaded panels
     *
     * @param string $name The name of the panel you want to get.
     * @return \DebugKit\DebugPanel|null The panel or null.
     */
    public function panel($name)
    {
        $registry = $this->service->registry();

        return $registry->{$name};
    }

    /**
     * Do the required setup work.
     *
     * - Build panels.
     * - Connect events
     *
     * @return void
     */
    public function setup()
    {
        $this->service->loadPanels();
    }

    /**
     * Call the initialize method onl all the loaded panels.
     *
     * @param \Cake\Event\Event $event The beforeDispatch event.
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        $this->service->initializePanels();
    }

    /**
     * Save the toolbar data.
     *
     * @param \Cake\Event\Event $event The afterDispatch event.
     * @return \Cake\Http\Response|null Modifed response or null
     */
    public function afterDispatch(Event $event)
    {
        /* @var Request $request */
        $request = $event->data['request'];
        /* @var Response $response */
        $response = $event->data['response'];
        $row = $this->service->saveData($request, $response);
        if (!$row) {
            return;
        }

        return $this->service->injectScripts($row, $response);
    }
}
