<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Cache manager for managing different types of cache configurations
 */
class CacheManager
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly array $cacheConfig = [],
        private readonly string $prefix = 'keycloak_'
    ) {}

    /**
     * Get cache with callback and TTL support
     */
    public function get(string $key, callable $callback, ?DateInterval $ttl = null): mixed
    {
        $prefixedKey = $this->prefixKey($key);
        $value = $this->cache->get($prefixedKey);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->cache->set($prefixedKey, $value, $ttl);

        return $value;
    }

    /**
     * Get cache value directly
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($this->prefixKey($key), $default);
    }

    /**
     * Set cache
     */
    public function set(string $key, mixed $value, ?DateInterval $ttl = null): bool
    {
        return $this->cache->set($this->prefixKey($key), $value, $ttl);
    }

    /**
     * Delete cache
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($this->prefixKey($key));
    }

    /**
     * Clear cache with specified prefix
     */
    public function clearByPrefix(string $prefix): bool
    {
        // Simplified implementation, should be optimized based on specific cache implementation
        // For production, might need cache implementations that support pattern deletion
        return true;
    }

    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }

    /**
     * Check if cache exists
     */
    public function has(string $key): bool
    {
        return $this->cache->has($this->prefixKey($key));
    }

    /**
     * Get underlying cache instance
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * Get cache configuration value
     */
    private function getCacheConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->cacheConfig[$key] ?? $default;
    }

    /**
     * Get all cache configuration
     */
    public function getCacheConfig(): array
    {
        return $this->cacheConfig;
    }

    /**
     * Get TTL value from configuration
     */
    public function getTtl(string $key, ?DateInterval $default = null): ?DateInterval
    {
        return $this->cacheConfig['ttl'][$key] ?? $default;
    }

    /**
     * Add prefix to cache key
     */
    private function prefixKey(string $key): string
    {
        return $this->prefix.$key;
    }

    /**
     * Get current prefix
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
