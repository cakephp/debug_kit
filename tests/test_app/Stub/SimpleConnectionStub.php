<?php
declare(strict_types=1);

namespace DebugKit\TestApp\Stub;

use Cake\Cache\Cache;
use Cake\Datasource\ConnectionInterface;
use Psr\SimpleCache\CacheInterface;
use stdClass;

class SimpleConnectionStub implements ConnectionInterface
{
    public function getDriver(string $role = self::ROLE_WRITE): object
    {
        return new stdClass();
    }

    public function setCacher(CacheInterface $cacher)
    {
        return $this;
    }

    public function getCacher(): CacheInterface
    {
        return Cache::pool('_simple_connection_stub_');
    }

    public function configName(): string
    {
        return 'simple_connection_stub';
    }

    public function config(): array
    {
        return [];
    }
}
