# Cache System

The Keycloak client now supports a powerful cache system based on the PSR-16 (Simple Cache) standard. The cache system can significantly improve performance by reducing API calls to the Keycloak server.

## Features

- **PSR-16 Compatible**: Based on PHP-FIG PSR-16 Simple Cache standard
- **Multiple Cache Implementations**: Memory cache, filesystem cache, with support for custom implementations
- **Flexible TTL Configuration**: Different data types can have different expiration times
- **Unified Token Storage**: Token storage is directly based on the cache layer
- **Cache Management**: Provides cache clearing, statistics and other management features

## Basic Usage

### Using Default Cache (Memory)

```php
use Overtrue\Keycloak\Keycloak;

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);

// Version information will be cached
$version = $keycloak->getVersion();

// ServerInfo will also be cached
$serverInfo = $keycloak->serverInfo()->get();
```

### Using Filesystem Cache

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Overtrue\Keycloak\Keycloak;

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new FilesystemAdapter())
);
```

### Custom Cache Configuration

```php
use DateInterval;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Overtrue\Keycloak\Keycloak;

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new ArrayAdapter()),
    cacheConfig: [
        'prefix' => 'myapp_',  // Custom prefix to avoid conflicts
        'ttl' => [
            'version' => new DateInterval('PT6H'),        // Cache version for 6 hours
            'server_info' => new DateInterval('PT1H'),    // Cache server info for 1 hour
            'access_token' => new DateInterval('PT1H'),   // Cache access token for 1 hour
            'refresh_token' => new DateInterval('P1D'),   // Cache refresh token for 1 day
        ]
    ]
);
```

## Cache Types

### 1. Version Cache
- **Key**: `version`
- **Default TTL**: 24 hours
- **Purpose**: Cache Keycloak server version information

### 2. ServerInfo Cache
- **Key**: `serverinfo`
- **Default TTL**: 1 hour
- **Purpose**: Cache server detailed information

### 3. Token Cache
- **Access Token Key**: `access_token`
- **Refresh Token Key**: `refresh_token`
- **Default TTL**: Access token 1 hour, refresh token 1 day
- **Purpose**: Cache authentication tokens

## Configuration Format

### TTL Configuration

TTL configuration uses nested format:

```php
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new ArrayAdapter()),
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('PT24H'),      // Cache version for 24 hours
            'server_info' => new DateInterval('PT1H'),   // Cache server info for 1 hour
            'access_token' => new DateInterval('PT1H'),  // Cache access token for 1 hour
            'refresh_token' => new DateInterval('P1D'),  // Cache refresh token for 1 day
        ]
    ]
);
```

### Prefix Configuration

To avoid cache key conflicts, you can set a custom prefix for cache keys:

```php
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new ArrayAdapter()),
    cacheConfig: [
        'prefix' => 'myapp_',  // Custom prefix, defaults to 'keycloak_'
        'ttl' => [
            'version' => new DateInterval('PT1H'),
            'server_info' => new DateInterval('PT30M'),
        ]
    ]
);
```

With prefix, internal cache keys automatically get the prefix:
- `version` → `myapp_version`
- `access_token` → `myapp_access_token`
- `serverinfo` → `myapp_serverinfo`

**Advantages of Prefix:**
- Avoid cache key conflicts with other applications
- Provide namespace isolation in shared cache environments (like Redis)
- Simplify key name management in application code

## Cache Management

### Clear Specific Cache

```php
// Clear version cache
$keycloak->clearVersionCache();

// Clear ServerInfo cache
$keycloak->serverInfo()->clearCache();
```

## Cache Implementations

### Symfony Cache (Recommended)

The project uses **Symfony Cache** as the cache engine by default, providing rich adapter choices.

#### ArrayAdapter (Default)
```php
use Overtrue\Keycloak\Keycloak;

// Uses ArrayAdapter by default
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);
```

#### FilesystemAdapter
```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$fileCache = new Psr16Cache(new FilesystemAdapter(
    namespace: 'keycloak_cache',
    defaultLifetime: 3600,
    directory: '/path/to/cache'
));

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: $fileCache
);
```

#### RedisAdapter
```php
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

$redis = RedisAdapter::createConnection('redis://localhost');
$redisCache = new Psr16Cache(new RedisAdapter($redis));

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: $redisCache
);
```

#### Available Adapters
- **ArrayAdapter** - Memory cache (default)
- **FilesystemAdapter** - Filesystem cache
- **ApcuAdapter** - APCu cache
- **RedisAdapter** - Redis cache
- **MemcachedAdapter** - Memcached cache
- **ChainAdapter** - Multi-level cache chain

### Custom Cache Implementation

You can implement your own cache class by implementing the `Psr\SimpleCache\CacheInterface` interface:

```php
use Psr\SimpleCache\CacheInterface;

class MyCacheImplementation implements CacheInterface
{
    // Implement interface methods...
}

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new MyCacheImplementation()
);
```

## Integration with Third-party Cache Libraries

Since it uses the PSR-16 standard, you can easily integrate with any compatible cache library:

### Laravel Cache

```php
use Illuminate\Support\Facades\Cache;

$cache = Cache::store(); // Laravel's cache instance

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: $cache
);
```

## Performance Optimization Recommendations

1. **Choose the Right Cache Implementation**:
   - Development environment: Use memory cache
   - Production environment: Use Redis or Memcached

2. **Set Reasonable TTL**:
   - Version information changes infrequently, can set longer TTL
   - Tokens should be adjusted based on expiration time
   - ServerInfo should be adjusted based on server stability

3. **Avoid Cache Avalanche**:
   - Set different TTLs for different types of data
   - Use randomized expiration times

## Migration Guide

### Migrating from Old Versions

If you were using an older version with TokenStorage, you now need to:

1. **Remove TokenStorage Parameter** - TokenStorage interface has been removed
2. **Use New Cache System** - Use cache parameter instead
3. **Use New Configuration Format** - Use nested TTL configuration format

```php
// Old version
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    tokenStorage: new MyTokenStorage()
);

// New version - Simplified constructor
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);

// New version - Using Symfony Cache with new configuration format
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new FilesystemAdapter()),
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('PT24H'),
            'server_info' => new DateInterval('PT1H'),
            'access_token' => new DateInterval('PT1H'),
            'refresh_token' => new DateInterval('P1D'),
        ]
    ]
);
```

### Migrating from Custom Cache Implementation (v2.0+)

From v2.0, the project uses **Symfony Cache** by default, custom cache implementations have been removed:

```php
// Old version - Custom implementation
use Overtrue\Keycloak\Cache\InMemoryCache;
use Overtrue\Keycloak\Cache\FilesystemCache;

$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new InMemoryCache()  // or new FilesystemCache('/path')
);

// New version - Symfony Cache (recommended)
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

// Memory cache (default, no need to specify explicitly)
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);

// Filesystem cache
$keycloak = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new FilesystemAdapter())
);
```

### Major Changes

- **Removed TokenStorage Interface** - Tokens are now stored directly in cache
- **Simplified Constructor** - Removed tokenStorage parameter
- **Unified Cache Management** - All cache (including tokens) managed through CacheManager
- **New Configuration Format** - TTL configuration uses nested structure, clearer and better organized

## Troubleshooting

### Cache Permission Issues

Ensure the cache directory has proper read/write permissions:

```bash
chmod 755 /path/to/cache/directory
```

### Cache Size Limits

For filesystem cache, be aware of disk space limits. You can periodically clean up:

```php
// Clean expired cache
$cache->clear();
```

### Debug Cache Issues

Check cache hit rates:

```php
// Check if cache exists
$exists = $cacheManager->has('version');
echo "Version cache exists: " . ($exists ? 'yes' : 'no');
```
