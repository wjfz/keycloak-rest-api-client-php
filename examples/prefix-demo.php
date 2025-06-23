<?php

declare(strict_types=1);

use DateInterval;
use Overtrue\Keycloak\Keycloak;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

require_once __DIR__.'/../vendor/autoload.php';

echo "=== Keycloak Cache Prefix Complete Demo ===\n";

// 1. Default prefix demo
echo "\n1. Default prefix 'keycloak_'\n";
$keycloak1 = new Keycloak(
    baseUrl: 'http://localhost:8080',
    username: 'admin',
    password: 'admin',
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('PT1H'),
        ],
    ]
);

echo "✓ Default prefix instance created successfully\n";
echo "  - version cache key becomes: keycloak_version\n";
echo "  - access_token cache key becomes: keycloak_access_token\n";

// 2. Custom prefix demo
echo "\n2. Custom prefix 'myapp_'\n";
$keycloak2 = new Keycloak(
    baseUrl: 'http://localhost:8080',
    username: 'admin',
    password: 'admin',
    cacheConfig: [
        'prefix' => 'myapp_',
        'ttl' => [
            'version' => new DateInterval('PT1H'),
        ],
    ]
);

echo "✓ Custom prefix instance created successfully\n";
echo "  - version cache key becomes: myapp_version\n";
echo "  - access_token cache key becomes: myapp_access_token\n";

// 3. Multi-application shared cache backend
echo "\n3. Multi-application shared cache backend\n";
$sharedCache = new Psr16Cache(new ArrayAdapter);

$appA = new Keycloak(
    baseUrl: 'http://localhost:8080',
    username: 'admin',
    password: 'admin',
    cache: $sharedCache,
    cacheConfig: [
        'prefix' => 'appA_',
        'ttl' => ['version' => new DateInterval('PT1H')],
    ]
);

$appB = new Keycloak(
    baseUrl: 'http://localhost:8080',
    username: 'admin',
    password: 'admin',
    cache: $sharedCache,
    cacheConfig: [
        'prefix' => 'appB_',
        'ttl' => ['version' => new DateInterval('PT1H')],
    ]
);

echo "✓ Two application instances share the same cache backend\n";
echo "  - Application A uses prefix: 'appA_'\n";
echo "  - Application B uses prefix: 'appB_'\n";
echo "  - Cache keys are completely isolated, no conflicts\n";

// 4. Cache management
echo "\n4. Cache management functionality\n";
$clearResult = $keycloak1->clearVersionCache();
echo '✓ Version cache clearing: '.($clearResult ? 'success' : 'failed')."\n";
echo "  - Only clears cache items with specified prefix\n";
echo "  - Other application caches are not affected\n";

// 5. Configuration options
echo "\n5. Configuration Options\n";
echo "Supported configuration methods:\n";
echo "```php\n";
echo "\$keycloak = new Keycloak(\n";
echo "    baseUrl: 'http://localhost:8080',\n";
echo "    username: 'admin',\n";
echo "    password: 'admin',\n";
echo "    cacheConfig: [\n";
echo "        'prefix' => 'custom_prefix_',  // Optional, defaults to 'keycloak_'\n";
echo "        'ttl' => [\n";
echo "            'version' => new DateInterval('PT1H'),\n";
echo "            'server_info' => new DateInterval('PT30M'),\n";
echo "            'access_token' => new DateInterval('PT1H'),\n";
echo "            'refresh_token' => new DateInterval('P1D'),\n";
echo "        ]\n";
echo "    ]\n";
echo ");\n";
echo "```\n";

echo "\n=== Advantages of Prefix Feature ===\n";
echo "✅ Namespace isolation: Avoid cache key conflicts between different applications\n";
echo "✅ Simplified key management: Use simple key names in application code, prefix added automatically\n";
echo "✅ Shared cache support: Multiple applications can safely share the same cache backend\n";
echo "✅ Easy maintenance: Unified prefix management, convenient for debugging and monitoring\n";

echo "\n=== Real-world Usage Recommendations ===\n";
echo "🔹 Development environment: Use application name as prefix, e.g., 'myapp_dev_'\n";
echo "🔹 Production environment: Use application name + version, e.g., 'myapp_v1_'\n";
echo "🔹 Multi-tenant: Use tenant ID as prefix, e.g., 'tenant123_'\n";
echo "🔹 Microservices: Use service name as prefix, e.g., 'userservice_'\n";

echo "\n🎉 Prefix functionality demonstration complete!\n";
