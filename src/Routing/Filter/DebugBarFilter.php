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

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\Routing\DispatcherFilter;
use Cake\Routing\Router;
use DebugKit\Panel\PanelRegistry;

/**
 * Toolbar injector filter.
 *
 * This class loads all the panels into the registry
 * and binds the correct events into the provided event
 * manager
 */
class DebugBarFilter extends DispatcherFilter
{
    use EventDispatcherTrait;

    /**
     * The panel registry.
     *
     * @var \DebugKit\Panel\PanelRegistry
     */
    protected $_registry;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'panels' => [
            'DebugKit.Cache',
            'DebugKit.Session',
            'DebugKit.Request',
            'DebugKit.SqlLog',
            'DebugKit.Timer',
            'DebugKit.Log',
            'DebugKit.Variables',
            'DebugKit.Environment',
            'DebugKit.Include',
            'DebugKit.History',
        ],
        'forceEnable' => false,
    ];

    /**
     * Constructor
     *
     * @param \Cake\Event\EventManager $events The event manager to use.
     * @param array $config The configuration data for DebugKit.
     */
    public function __construct(EventManager $events, array $config)
    {
        parent::__construct($config);

        $this->eventManager($events);
        $this->_registry = new PanelRegistry($events);
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
        $enabled = (bool)Configure::read('debug');
        if ($enabled) {
            return true;
        }
        $force = $this->config('forceEnable');
        if (is_callable($force)) {
            return $force();
        }

        return $force;
    }

    /**
     * Get the list of loaded panels
     *
     * @return array
     */
    public function loadedPanels()
    {
        return $this->_registry->loaded();
    }

    /**
     * Get the list of loaded panels
     *
     * @param string $name The name of the panel you want to get.
     * @return \DebugKit\DebugPanel|null The panel or null.
     */
    public function panel($name)
    {
        return $this->_registry->{$name};
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
        foreach ($this->config('panels') as $panel) {
            $this->_registry->load($panel);
        }
    }

    /**
     * Call the initialize method onl all the loaded panels.
     *
     * @param \Cake\Event\Event $event The beforeDispatch event.
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        foreach ($this->_registry->loaded() as $panel) {
            $this->_registry->{$panel}->initialize();
        }
    }

    /**
     * Save the toolbar data.
     *
     * @param \Cake\Event\Event $event The afterDispatch event.
     * @return void
     */
    public function afterDispatch(Event $event)
    {
        /* @var Request $request */
        $request = $event->data['request'];
        // Skip debugkit requests and requestAction()
        if ($request->param('plugin') === 'DebugKit' || $request->is('requested')) {
            return;
        }
        /* @var Response $response */
        $response = $event->data['response'];

        $data = [
            'url' => $request->here(),
            'content_type' => $response->type(),
            'method' => $request->method(),
            'status_code' => $response->statusCode(),
            'requested_at' => $request->env('REQUEST_TIME'),
            'panels' => []
        ];
        /* @var \DebugKit\Model\Table\RequestsTable $requests */
        $requests = TableRegistry::get('DebugKit.Requests');
        $requests->gc();

        $row = $requests->newEntity($data);
        $row->isNew(true);

        foreach ($this->_registry->loaded() as $name) {
            $panel = $this->_registry->{$name};
            $content = $this->_serialize($panel->data());
            $row->panels[] = $requests->Panels->newEntity([
                'panel' => $name,
                'element' => $panel->elementName(),
                'title' => $panel->title(),
                'summary' => $panel->summary(),
                'content' => $content,
            ]);
        }
        $row = $requests->save($row);

        $this->_injectScripts($row->id, $response);
        $response->header(['X-DEBUGKIT-ID' => $row->id]);
    }

    /**
     * Serialization wrapper for toolbar data
     *
     * @param array $data Panel data to serialize.
     * @return string serialized panel data
     */
    protected function _serialize($data)
    {
        $response = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $response[$key] = [];
                foreach ($value as $k => $v) {
                    try {
                        serialize($v);
                        $response[$key][$k] = $v;
                    } catch (\Exception $e) {
                        $response[$key][$k] = $e->getMessage();
                        $response['error'] = $e->getMessage();
                    }
                }
            } else {
                try {
                    serialize($value);
                    $response[$key] = $value;
                } catch (\Exception $e) {
                    $response['error'] = $e->getMessage();
                }
            }
        }

        return serialize($response);
    }

    /**
     * Injects the JS to build the toolbar.
     *
     * The toolbar will only be injected if the response's content type
     * contains HTML and there is a </body> tag.
     *
     * @param string $id ID to fetch data from.
     * @param \Cake\Network\Response $response The response to augment.
     * @return void
     */
    protected function _injectScripts($id, $response)
    {
        if (strpos($response->type(), 'html') === false) {
            return;
        }
        $body = $response->body();
        if (!is_string($body)) {
            return;
        }
        $pos = strrpos($body, '</body>');
        if ($pos === false) {
            return;
        }
        $url = Router::url('/', true);
        $script = "<script id=\"__debug_kit\" data-id=\"{$id}\" data-url=\"{$url}\" src=\"" . Router::url('/debug_kit/js/toolbar.js') . '"></script>';
        $body = substr($body, 0, $pos) . $script . substr($body, $pos);
        $response->body($body);
    }
}
