<?php

namespace App\Support\Repositories;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

abstract class CacheableRepository
{
    protected CacheRepository $cache;

    protected int $ttlShort = 300;    // 5 min
    protected int $ttlMedium = 3600;  // 1 hour
    protected int $ttlLong = 21600;   // 6 hours

    public function __construct()
    {
        $this->cache = $this->resolveCache();
    }

    /**
     * Each repository defines its own cache namespace
     */
    abstract protected function cacheTags(): array;

    protected function resolveCache(): CacheRepository
    {
        $store = Cache::getStore();

        if ($store instanceof TaggableStore) {
            return Cache::tags($this->cacheTags());
        }

        return Cache::store();
    }

    protected function remember(string $key, int $ttl, \Closure $callback)
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    protected function flushCache(): void
    {
        $this->cache->flush();
    }

    protected function forget(string $key): void
    {
        $this->cache->forget($key);
    }
}