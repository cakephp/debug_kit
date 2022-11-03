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

use Cake\Core\App;
use Cake\Core\ObjectRegistry;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventManager;

/**
 * Registry object for panels.
 */
class PanelRegistry extends ObjectRegistry implements EventDispatcherInterface
{
    use EventDispatcherTrait;

    /**
     * Constructor
     *
     * @param \Cake\Event\EventManager $events Event Manager that panels should bind to.
     *   Typically this is the global manager.
     */
    public function __construct(EventManager $events)
    {
        $this->setEventManager($events);
    }

    /**
     * Resolve a panel class name.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class Partial class name to resolve.
     * @return string|null Either the correct class name, null if the class is not found.
     */
    protected function _resolveClassName(string $class): ?string
    {
        return App::className($class, 'Panel', 'Panel');
    }

    /**
     * Throws an exception when a component is missing.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the component is missing in.
     * @return void
     * @throws \RuntimeException
     */
    protected function _throwMissingClassError(string $class, ?string $plugin): void
    {
        throw new \RuntimeException(sprintf("Unable to find '%s' panel.", $class));
    }

    /**
     * Create the panels instance.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class The classname to create.
     * @param string $alias The alias of the panel.
     * @param array $config An array of config to use for the panel.
     * @return \DebugKit\DebugPanel The constructed panel class.
     * @psalm-param class-string<\DebugKit\DebugPanel> $class
     */
    protected function _create($class, string $alias, array $config)
    {
        $instance = new $class($this, $config);
        $this->getEventManager()->on($instance);

        return $instance;
    }
}
