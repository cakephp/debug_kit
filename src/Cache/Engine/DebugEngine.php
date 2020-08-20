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
namespace DebugKit\Cache\Engine;

use Cake\Cache\CacheEngine;
use Cake\Cache\CacheRegistry;
use Psr\Log\LoggerInterface;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $name;

    /**
     * Hit/miss metrics.
     *
     * @var mixed
     */
    protected $metrics = [
        'set' => 0,
        'delete' => 0,
        'get hit' => 0,
        'get miss' => 0,
    ];

    /**
     * Constructor
     *
     * @param mixed $config Config data or the proxied adapter.
     * @param string $name The name of the proxied cache engine.
     * @param \Psr\Log\LoggerInterface $logger Logger for collecting cache operation logs.
     */
    public function __construct($config, string $name, LoggerInterface $logger)
    {
        $this->_config = $config;
        $this->logger = $logger;
        $this->name = $name;
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
        return $this->metrics;
    }

    /**
     * Track a metric.
     *
     * @param string $metric The metric to increment.
     * @return void
     */
    protected function track($metric)
    {
        $this->metrics[$metric]++;
    }

    /**
     * Log a cache operation
     *
     * @param string $operation The operation performed.
     * @param float $duration The duration of the operation.
     * @param string|null $key The cache key.
     * @return void
     */
    protected function log(string $operation, float $duration, ?string $key = null): void
    {
        $key = $key ? " `{$key}`" : '';
        $duration = number_format($duration, 5);
        $this->logger->log('info', ":{$this->name}: {$operation}{$key} - {$duration}ms");
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        $start = microtime(true);
        $result = $this->_engine->set($key, $value, $ttl);
        $duration = microtime(true) - $start;

        $this->track('set');
        $this->log('set', $duration, $key);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $start = microtime(true);
        $result = $this->_engine->setMultiple($values);
        $duration = microtime(true) - $start;

        $this->track('set');
        $this->log('setMultiple', $duration);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $start = microtime(true);
        $result = $this->_engine->get($key, $default);
        $duration = microtime(true) - $start;
        $metric = 'hit';
        if ($result === null) {
            $metric = 'miss';
        }

        $this->track("get {$metric}");
        $this->log('get', $duration, $key);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $start = microtime(true);
        $result = $this->_engine->getMultiple($keys);
        $duration = microtime(true) - $start;

        $this->track('get hit');
        $this->log('getMultiple', $duration);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $offset = 1)
    {
        $start = microtime(true);
        $result = $this->_engine->increment($key, $offset);
        $duration = microtime(true) - $start;

        $this->track('set');
        $this->log('increment', $duration, $key);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $offset = 1)
    {
        $start = microtime(true);
        $result = $this->_engine->decrement($key, $offset);
        $duration = microtime(true) - $start;

        $this->track('set');
        $this->log('decrement', $duration, $key);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        $start = microtime(true);
        $result = $this->_engine->delete($key);
        $duration = microtime(true) - $start;

        $this->track('delete');
        $this->log('delete', $duration, $key);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($data): bool
    {
        $start = microtime(true);
        $result = $this->_engine->deleteMultiple($data);
        $duration = microtime(true) - $start;

        $this->track('delete');
        $this->log('deleteMultiple', $duration);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $start = microtime(true);
        $result = $this->_engine->clear();
        $duration = microtime(true) - $start;

        $this->track('delete');
        $this->log('clear', $duration);

        return $result;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function clearGroup(string $group): bool
    {
        $start = microtime(true);
        $result = $this->_engine->clearGroup($group);
        $duration = microtime(true) - $start;

        $this->track('delete');
        $this->log('clearGroup', $duration, $group);

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
