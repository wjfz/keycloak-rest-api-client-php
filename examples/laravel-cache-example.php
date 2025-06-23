<?php

declare(strict_types=1);

use DateInterval;
use Overtrue\Keycloak\Keycloak;

require_once __DIR__.'/../vendor/autoload.php';

echo "=== Laravel Cache Integration Example ===\n";

// Example 1: Using Laravel's Cache facade in a Laravel application
// Note: This would typically be used in a Laravel service provider or controller

/*
// In your Laravel application (e.g., Service Provider or Controller):

use Illuminate\Support\Facades\Cache;
use Overtrue\Keycloak\Keycloak;

// Laravel's Cache facade implements PSR-16 CacheInterface
$keycloak = new Keycloak(
    baseUrl: config('keycloak.base_url', 'http://keycloak:8080'),
    username: config('keycloak.username', 'admin'),
    password: config('keycloak.password', 'admin'),
    cache: Cache::store(), // Use default cache store
    cacheConfig: [
        'prefix' => 'keycloak_',
        'ttl' => [
            'version' => new DateInterval('PT2H'),
            'server_info' => new DateInterval('PT1H'),
            'access_token' => new DateInterval('PT1H'),
            'refresh_token' => new DateInterval('P1D'),
        ]
    ]
);

// Now you can use Keycloak with Laravel's cache
$version = $keycloak->getVersion();
*/

// Example 2: Simulating Laravel Cache for demonstration
// This creates a mock of how it would work in a Laravel environment

class MockLaravelCache implements \Psr\SimpleCache\CacheInterface
{
    private array $cache = [];

    private array $ttls = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->has($key)) {
            return $default;
        }

        return $this->cache[$key];
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->cache[$key] = $value;
        if ($ttl !== null) {
            $expiry = time();
            if ($ttl instanceof DateInterval) {
                $expiry += $ttl->s + ($ttl->i * 60) + ($ttl->h * 3600) + ($ttl->d * 86400);
            } elseif (is_int($ttl)) {
                $expiry += $ttl;
            }
            $this->ttls[$key] = $expiry;
        }

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->cache[$key], $this->ttls[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->cache = [];
        $this->ttls = [];

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has(string $key): bool
    {
        if (! isset($this->cache[$key])) {
            return false;
        }

        if (isset($this->ttls[$key]) && time() > $this->ttls[$key]) {
            unset($this->cache[$key], $this->ttls[$key]);

            return false;
        }

        return true;
    }
}

// Demonstration using mock Laravel cache
echo "\n1. Creating Keycloak instance with Laravel-like cache\n";

$laravelCache = new MockLaravelCache;

$keycloak = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://localhost:8080',
    username: 'admin',
    password: 'admin',
    cache: $laravelCache,
    cacheConfig: [
        'prefix' => 'laravel_keycloak_', // Prefix to avoid conflicts
        'ttl' => [
            'version' => new DateInterval('PT2H'),        // Cache version for 2 hours
            'server_info' => new DateInterval('PT1H'),    // Cache server info for 1 hour
            'access_token' => new DateInterval('PT1H'),   // Cache access token for 1 hour
            'refresh_token' => new DateInterval('P1D'),   // Cache refresh token for 1 day
        ],
    ]
);

echo "✓ Keycloak instance created with Laravel-style cache\n";
echo "✓ Cache prefix: 'laravel_keycloak_' to avoid conflicts with other Laravel cache keys\n";

// Example 3: Cache configuration for different Laravel environments
echo "\n2. Environment-specific configurations\n";

$configurations = [
    'local' => [
        'cache_store' => 'array',
        'prefix' => 'local_keycloak_',
        'ttl' => [
            'version' => new DateInterval('PT30M'),      // Shorter TTL for development
            'server_info' => new DateInterval('PT15M'),
        ],
    ],
    'staging' => [
        'cache_store' => 'redis',
        'prefix' => 'staging_keycloak_',
        'ttl' => [
            'version' => new DateInterval('PT1H'),
            'server_info' => new DateInterval('PT30M'),
        ],
    ],
    'production' => [
        'cache_store' => 'redis',
        'prefix' => 'prod_keycloak_',
        'ttl' => [
            'version' => new DateInterval('PT6H'),       // Longer TTL for production
            'server_info' => new DateInterval('PT2H'),
        ],
    ],
];

foreach ($configurations as $env => $config) {
    echo "✓ {$env}: Store={$config['cache_store']}, Prefix={$config['prefix']}\n";
}

// Example 4: Laravel Service Provider integration
echo "\n3. Laravel Service Provider Example\n";
echo "```php\n";
echo "// In AppServiceProvider or dedicated KeycloakServiceProvider:\n";
echo "\n";
echo "public function register()\n";
echo "{\n";
echo "    \$this->app->singleton(Keycloak::class, function (\$app) {\n";
echo "        return new Keycloak(\n";
echo "            baseUrl: config('keycloak.base_url'),\n";
echo "            username: config('keycloak.username'),\n";
echo "            password: config('keycloak.password'),\n";
echo "            cache: Cache::store(config('keycloak.cache_store', 'redis')),\n";
echo "            cacheConfig: [\n";
echo "                'prefix' => config('keycloak.cache_prefix', 'keycloak_'),\n";
echo "                'ttl' => [\n";
echo "                    'version' => new DateInterval(config('keycloak.ttl.version', 'PT6H')),\n";
echo "                    'server_info' => new DateInterval(config('keycloak.ttl.server_info', 'PT1H')),\n";
echo "                    'access_token' => new DateInterval(config('keycloak.ttl.access_token', 'PT1H')),\n";
echo "                    'refresh_token' => new DateInterval(config('keycloak.ttl.refresh_token', 'P1D')),\n";
echo "                ]\n";
echo "            ]\n";
echo "        );\n";
echo "    });\n";
echo "}\n";
echo "```\n";

// Example 5: Configuration file
echo "\n4. Laravel Configuration File (config/keycloak.php)\n";
echo "```php\n";
echo "return [\n";
echo "    'base_url' => env('KEYCLOAK_BASE_URL', 'http://keycloak:8080'),\n";
echo "    'username' => env('KEYCLOAK_USERNAME', 'admin'),\n";
echo "    'password' => env('KEYCLOAK_PASSWORD', 'admin'),\n";
echo "    \n";
echo "    // Cache configuration\n";
echo "    'cache_store' => env('KEYCLOAK_CACHE_STORE', 'redis'),\n";
echo "    'cache_prefix' => env('KEYCLOAK_CACHE_PREFIX', 'keycloak_'),\n";
echo "    \n";
echo "    'ttl' => [\n";
echo "        'version' => env('KEYCLOAK_TTL_VERSION', 'PT6H'),\n";
echo "        'server_info' => env('KEYCLOAK_TTL_SERVER_INFO', 'PT1H'),\n";
echo "        'access_token' => env('KEYCLOAK_TTL_ACCESS_TOKEN', 'PT1H'),\n";
echo "        'refresh_token' => env('KEYCLOAK_TTL_REFRESH_TOKEN', 'P1D'),\n";
echo "    ],\n";
echo "];\n";
echo "```\n";

echo "\n=== Benefits of Laravel Cache Integration ===\n";
echo "✅ Unified caching: Uses Laravel's cache system consistently\n";
echo "✅ Configuration management: Leverage Laravel's config system\n";
echo "✅ Multiple stores: Redis, Memcached, Database, File, Array\n";
echo "✅ Cache tagging: Group related cache items (if supported by store)\n";
echo "✅ Cache events: Listen to cache operations for logging/debugging\n";
echo "✅ Artisan commands: Use Laravel's cache:clear, cache:forget commands\n";

echo "\n=== Usage in Laravel Controllers ===\n";
echo "```php\n";
echo "class KeycloakController extends Controller\n";
echo "{\n";
echo "    public function __construct(private Keycloak \$keycloak) {}\n";
echo "    \n";
echo "    public function getServerInfo()\n";
echo "    {\n";
echo "        // This will use Laravel's cache automatically\n";
echo "        \$serverInfo = \$this->keycloak->serverInfo()->get();\n";
echo "        \n";
echo "        return response()->json([\n";
echo "            'version' => \$serverInfo->getSystemInfo()->getVersion(),\n";
echo "            'uptime' => \$serverInfo->getSystemInfo()->getUptime(),\n";
echo "        ]);\n";
echo "    }\n";
echo "}\n";
echo "```\n";

echo "\n🎉 Laravel Cache integration example complete!\n";
