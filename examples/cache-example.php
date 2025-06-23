<?php

declare(strict_types=1);

use DateInterval;
use Overtrue\Keycloak\Keycloak;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require_once __DIR__.'/../vendor/autoload.php';

// Example 1: Using memory cache (default)
echo "=== Using Memory Cache ===\n";

$keycloak = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new ArrayAdapter,
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('PT2H'),        // Cache version for 2 hours
            'server_info' => new DateInterval('PT30M'),   // Cache server info for 30 minutes
            'access_token' => new DateInterval('PT1H'),   // Cache access token for 1 hour
            'refresh_token' => new DateInterval('P1D'),   // Cache refresh token for 1 day
        ],
    ]
);

// First call - will fetch from API
$start = microtime(true);
$version1 = $keycloak->getVersion();
$time1 = microtime(true) - $start;
echo "First version fetch: {$version1} (time: ".round($time1 * 1000, 2)."ms)\n";

// Second call - will fetch from cache
$start = microtime(true);
$version2 = $keycloak->getVersion();
$time2 = microtime(true) - $start;
echo "Second version fetch: {$version2} (time: ".round($time2 * 1000, 2)."ms)\n";

// Get ServerInfo
$start = microtime(true);
$serverInfo = $keycloak->serverInfo()->get();
$time3 = microtime(true) - $start;
echo 'First ServerInfo fetch (time: '.round($time3 * 1000, 2)."ms)\n";

// Get ServerInfo again - from cache
$start = microtime(true);
$serverInfo2 = $keycloak->serverInfo()->get();
$time4 = microtime(true) - $start;
echo 'Second ServerInfo fetch (time: '.round($time4 * 1000, 2)."ms)\n";

// Cache is enabled
echo "Cache enabled\n";

echo "\n=== Using Filesystem Cache ===\n";

// Example 2: Using filesystem cache
$cacheDir = sys_get_temp_dir().'/keycloak_cache';
$keycloak2 = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new FilesystemAdapter('keycloak', 0, $cacheDir),
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('PT6H'),        // Cache version for 6 hours
            'server_info' => new DateInterval('PT1H'),    // Cache server info for 1 hour
        ],
    ]
);

$version3 = $keycloak2->getVersion();
echo "Version fetched with filesystem cache: {$version3}\n";

// Filesystem cache enabled
echo "Filesystem cache enabled\n";

echo "\n=== Cache Management ===\n";

// Clear version cache
$cleared = $keycloak->clearVersionCache();
echo 'Clear version cache: '.($cleared ? 'success' : 'failed')."\n";

// Clear ServerInfo cache
$cleared = $keycloak->serverInfo()->clearCache();
echo 'Clear ServerInfo cache: '.($cleared ? 'success' : 'failed')."\n";

echo "\n=== Prefix Feature Demo ===\n";

// Create Keycloak instance with custom prefix
$keycloakWithPrefix = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new ArrayAdapter,
    cacheConfig: [
        'prefix' => 'myapp_',  // Custom prefix
        'ttl' => [
            'version' => new DateInterval('PT1H'),
        ],
    ]
);

$versionWithPrefix = $keycloakWithPrefix->getVersion();
echo "Version fetched with custom prefix 'myapp_': {$versionWithPrefix}\n";
echo "Actual cache key: myapp_version (avoids key conflicts)\n";

echo "\n🎉 Cache system demonstration complete!\n";
