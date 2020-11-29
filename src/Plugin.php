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
 * @since         DebugKit 3.15.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use DebugKit\Command\BenchmarkCommand;
use DebugKit\Middleware\DebugKitMiddleware;
use DebugKit\Panel\DeprecationsPanel;

/**
 * Plugin class for CakePHP plugin collection.
 */
class Plugin extends BasePlugin
{
    /**
     * @var \DebugKit\ToolbarService
     */
    protected $service;

    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        $service = new ToolbarService(EventManager::instance(), (array)Configure::read('DebugKit'));

        if (!$service->isEnabled() || php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg') {
            return;
        }

        $this->service = $service;

        $this->setDeprecationHandler($service);

        // will load `config/bootstrap.php`.
        parent::bootstrap($app);
    }

    /**
     * Add middleware for the plugin.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to update.
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        if ($this->service) {
            $middleware->insertAt(0, new DebugKitMiddleware($this->service));
        }

        return $middleware;
    }

    /**
     * Add console commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection $commands The command collection to update
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands->add('benchmark', BenchmarkCommand::class);
    }

    /**
     * set deprecation handler
     *
     * @param \DebugKit\ToolbarService $service The toolbar service instance
     * @return void
     */
    public function setDeprecationHandler($service)
    {
        if (!empty($service->getConfig('panels')['DebugKit.Deprecations'])) {
            $previousHandler = set_error_handler(
                function ($code, $message, $file, $line, $context = null) use (&$previousHandler) {
                    if ($code == E_USER_DEPRECATED || $code == E_DEPRECATED) {
                        DeprecationsPanel::addDeprecatedError(compact('code', 'message', 'file', 'line', 'context'));

                        return;
                    }
                    if ($previousHandler) {
                        $context['_trace_frame_offset'] = 1;

                        return $previousHandler($code, $message, $file, $line, $context);
                    }

                    return false;
                }
            );
        }
    }
}
