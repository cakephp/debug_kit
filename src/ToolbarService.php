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
namespace DebugKit;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use DebugKit\Panel\PanelRegistry;
use Psr\Http\Message\ResponseInterface;

/**
 * Used to create the panels and inject a toolbar into
 * matching responses.
 *
 * Used by the Routing Filter and Middleware.
 */
class ToolbarService
{
    use InstanceConfigTrait;

    /**
     * The panel registry.
     *
     * @var \DebugKit\Panel\PanelRegistry
     */
    protected $registry;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'panels' => [
            'DebugKit.Cache' => true,
            'DebugKit.Session' => true,
            'DebugKit.Request' => true,
            'DebugKit.SqlLog' => true,
            'DebugKit.Timer' => true,
            'DebugKit.Log' => true,
            'DebugKit.Variables' => true,
            'DebugKit.Environment' => true,
            'DebugKit.Include' => true,
            'DebugKit.History' => true,
            'DebugKit.Routes' => true,
            'DebugKit.Packages' => true,
            'DebugKit.Mail' => true,
        ],
        'forceEnable' => false,
    ];

    /**
     * Constructor
     *
     * @param \Cake\Event\EventManager $events The event manager to use defaults to the global manager
     * @param array $config The configuration data for DebugKit.
     */
    public function __construct(EventManager $events, array $config)
    {
        $this->config($config);
        $this->registry = new PanelRegistry($events);
    }

    /**
     * Fetch the PanelRegistry
     *
     * @return \DebugKit\Panel\PanelRegistry
     */
    public function registry()
    {
        return $this->registry;
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
        return $this->registry->loaded();
    }

    /**
     * Get the list of loaded panels
     *
     * @param string $name The name of the panel you want to get.
     * @return \DebugKit\DebugPanel|null The panel or null.
     */
    public function panel($name)
    {
        return $this->registry->{$name};
    }

    /**
     * Load all the panels being used
     *
     * @return void
     */
    public function loadPanels()
    {
        foreach ($this->config('panels') as $panel => $enabled) {
            list($panel, $enabled) = (is_numeric($panel)) ? [$enabled, true] : [$panel, $enabled];
            if ($enabled) {
                $this->registry->load($panel);
            }
        }
    }

    /**
     * Call the initialize method onl all the loaded panels.
     *
     * @return void
     */
    public function initializePanels()
    {
        foreach ($this->registry->loaded() as $panel) {
            $this->registry->{$panel}->initialize();
        }
    }

    /**
     * Save the toolbar state.
     *
     * @param \Cake\Http\ServerRequest $request The request
     * @param \Psr\Http\Message\ResponseInterface $response The response
     * @return null|\DebugKit\Model\Entity\Request Saved request data.
     */
    public function saveData(ServerRequest $request, ResponseInterface $response)
    {
        // Skip debugkit requests and requestAction()
        $path = $request->getUri()->getPath();
        if (strpos($path, 'debug_kit') !== false ||
            strpos($path, 'debug-kit') !== false ||
            $request->is('requested')
        ) {
            return null;
        }
        $data = [
            'url' => $request->getUri()->getPath(),
            'content_type' => $response->getHeaderLine('Content-Type'),
            'method' => $request->getMethod(),
            'status_code' => $response->getStatusCode(),
            'requested_at' => $request->env('REQUEST_TIME'),
            'panels' => []
        ];
        /* @var \DebugKit\Model\Table\RequestsTable $requests */
        $requests = TableRegistry::get('DebugKit.Requests');
        $requests->gc();

        $row = $requests->newEntity($data);
        $row->isNew(true);

        foreach ($this->registry->loaded() as $name) {
            $panel = $this->registry->{$name};
            try {
                $content = serialize($panel->data());
            } catch (\Exception $e) {
                $content = serialize([
                    'error' => $e->getMessage(),
                ]);
            }
            $row->panels[] = $requests->Panels->newEntity([
                'panel' => $name,
                'element' => $panel->elementName(),
                'title' => $panel->title(),
                'summary' => $panel->summary(),
                'content' => $content,
            ]);
        }

        return $requests->save($row);
    }

    /**
     * Injects the JS to build the toolbar.
     *
     * The toolbar will only be injected if the response's content type
     * contains HTML and there is a </body> tag.
     *
     * @param \DebugKit\Model\Entity\Request $row The request data to inject.
     * @param \Psr\Http\Message\ResponseInterface $response The response to augment.
     * @return \Psr\Http\Message\ResponseInterface The modified response
     */
    public function injectScripts($row, ResponseInterface $response)
    {
        $response = $response->withHeader('X-DEBUGKIT-ID', $row->id);
        if (strpos($response->getHeaderLine('Content-Type'), 'html') === false) {
            return $response;
        }
        $body = $response->getBody();
        if (!$body->isSeekable() || !$body->isWritable()) {
            return $response;
        }
        $body->rewind();
        $contents = $body->getContents();

        $pos = strrpos($contents, '</body>');
        if ($pos === false) {
            return $response;
        }

        $url = Router::url('/', true);
        $script = sprintf(
            '<script id="__debug_kit" data-id="%s" data-url="%s" src="%s"></script>',
            $row->id,
            $url,
            Router::url('/debug_kit/js/toolbar.js')
        );
        $contents = substr($contents, 0, $pos) . $script . substr($contents, $pos);
        $body->rewind();
        $body->write($contents);

        return $response->withBody($body);
    }
}
