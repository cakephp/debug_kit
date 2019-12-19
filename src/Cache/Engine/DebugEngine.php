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
 *
 */
namespace DebugKit\Cache\Engine;

use Cake\Cache\CacheEngine;
use Cake\Cache\CacheRegistry;
use DebugKit\DebugTimer;

/**
 * A spying proxy for cache engines.
 *
 * Used by the CachePanel to wrap and track metrics related to caching.
 */
class DebugEngine extends CacheEngine
{
    /**
     * Proxied cache engine config.
     *
     * @var mixed
     */
    protected $_config;

    /**
     * Proxied engine
     *
     * @var mixed
     */
    protected $_engine;

    /**
     * Hit/miss metrics.
     *
     * @var mixed
     */
    protected $_metrics = [
        'set' => 0,
        'delete' => 0,
        'get' => 0,
        'hit' => 0,
        'miss' => 0,
    ];

    /**
     * Constructor
     *
     * @param mixed $config Config data or the proxied adapter.
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Initialize the proxied Cache Engine
     *
     * @param array $config Array of setting for the engine.
     * @return bool True, this engine cannot fail to initialize.
     */
    public function init(array $config = []): bool
    {
        if (is_object($this->_config)) {
            $this->_engine = $this->_config;

            return true;
        }
        $registry = new CacheRegistry();
        $this->_engine = $registry->load('spies', $this->_config);
        unset($registry);

        return true;
    }

    /**
     * Get the internal engine
     *
     * @return \Cake\Cache\CacheEngine
     */
    public function engine()
    {
        return $this->_engine;
    }

    /**
     * Get the metrics for this object.
     *
     * @return array
     */
    public function metrics()
    {
        return $this->_metrics;
    }

    /**
     * Track a metric.
     *
     * @param string $metric The metric to increment.
     * @return void
     */
    protected function _track($metric)
    {
        $this->_metrics[$metric]++;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->_track('set');
        DebugTimer::start('Cache.set ' . $key);
        $result = $this->_engine->set($key, $value, $ttl);
        DebugTimer::stop('Cache.set ' . $key);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($data, $ttl = null): bool
    {
        $this->_track('set');
        DebugTimer::start('Cache.setMultiple');
        $result = $this->_engine->setMultiple($data);
        DebugTimer::stop('Cache.setMultiple');

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->_track('get');
        DebugTimer::start('Cache.get ' . $key);
        $result = $this->_engine->get($key, $default);
        DebugTimer::stop('Cache.get ' . $key);
        $metric = 'hit';
        if ($result === false) {
            $metric = 'miss';
        }
        $this->_track($metric);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $this->_track('get');
        DebugTimer::start('Cache.getMultiple');
        $result = $this->_engine->getMultiple($keys);
        DebugTimer::stop('Cache.getMultiple');

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $offset = 1)
    {
        $this->_track('set');
        DebugTimer::start('Cache.increment ' . $key);
        $result = $this->_engine->increment($key, $offset);
        DebugTimer::stop('Cache.increment ' . $key);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function decrement(string $key, int $offset = 1)
    {
        $this->_track('set');
        DebugTimer::start('Cache.decrement ' . $key);
        $result = $this->_engine->decrement($key, $offset);
        DebugTimer::stop('Cache.decrement ' . $key);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key): bool
    {
        $this->_track('delete');
        DebugTimer::start('Cache.delete ' . $key);
        $result = $this->_engine->delete($key);
        DebugTimer::stop('Cache.delete ' . $key);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($data): bool
    {
        $this->_track('delete');
        DebugTimer::start('Cache.deleteMultiple');
        $result = $this->_engine->deleteMultiple($data);
        DebugTimer::stop('Cache.deleteMultiple');

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        $this->_track('delete');
        DebugTimer::start('Cache.clear');
        $result = $this->_engine->clear();
        DebugTimer::stop('Cache.clear');

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function groups(): array
    {
        return $this->_engine->groups();
    }

    /**
     * Returns the config.
     *
     * @param string|null $key The key to get or null for the whole config.
     * @param mixed $default The return value when the key does not exist.
     * @return mixed Config value being read.
     */
    public function getConfig(?string $key = null, $default = null)
    {
        return $this->_engine->getConfig($key, $default);
    }

    /**
     * Sets the config.
     *
     * @param string|array $key The key to set, or a complete array of configs.
     * @param mixed|null $value The value to set.
     * @param bool $merge Whether to recursively merge or overwrite existing config, defaults to true.
     * @return $this
     * @throws \Cake\Core\Exception\Exception When trying to set a key that is invalid.
     */
    public function setConfig($key, $value = null, $merge = true)
    {
        return $this->_engine->setConfig($key, $value, $merge);
    }

    /**
     * {@inheritDoc}
     */
    public function clearGroup(string $group): bool
    {
        $this->_track('delete');
        DebugTimer::start('Cache.clearGroup ' . $group);
        $result = $this->_engine->clearGroup($group);
        DebugTimer::stop('Cache.clearGroup ' . $group);

        return $result;
    }

    /**
     * Magic __toString() method to get the CacheEngine's name
     *
     * @return string Returns the CacheEngine's name
     */
    public function __toString()
    {
        if (!empty($this->_engine)) {
            [$ns, $class] = namespaceSplit(get_class($this->_engine));

            return str_replace('Engine', '', $class);
        }

        return $this->_config['className'];
    }
}
