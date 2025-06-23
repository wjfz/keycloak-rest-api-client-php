<?php

require_once __DIR__.'/../vendor/autoload.php';

use DateInterval;
use Overtrue\Keycloak\Keycloak;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

echo "=== Symfony Cache 适配器示例 ===\n\n";

// 1. 默认使用 ArrayAdapter (内存缓存)
echo "1. 使用默认 ArrayAdapter:\n";
$keycloak1 = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);
echo "✓ 默认使用 Symfony ArrayAdapter (内存缓存)\n\n";

// 2. 使用 FilesystemAdapter (文件系统缓存)
echo "2. 使用 FilesystemAdapter:\n";
$fileCache = new Psr16Cache(new FilesystemAdapter(
    namespace: 'keycloak_cache',
    defaultLifetime: 3600,
    directory: sys_get_temp_dir().'/keycloak-cache'
));

$keycloak2 = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: $fileCache
);
echo '✓ 使用文件系统缓存，缓存目录: '.sys_get_temp_dir()."/keycloak-cache\n\n";

// 3. 使用 APCu (如果可用)
echo "3. APCu 缓存支持检查:\n";
if (extension_loaded('apcu') && apcu_enabled()) {
    $apcuCache = new Psr16Cache(new ApcuAdapter('keycloak_'));

    $keycloak3 = new Keycloak(
        baseUrl: 'http://keycloak:8080',
        username: 'admin',
        password: 'admin',
        cache: $apcuCache
    );
    echo "✓ APCu 可用，使用 APCu 缓存\n";
} else {
    echo "⚠ APCu 不可用或未启用\n";
}
echo "\n";

// 4. 自定义 TTL 配置
echo "4. 自定义缓存 TTL 配置:\n";
$keycloak4 = new Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new ArrayAdapter),
    cacheConfig: [
        'ttl' => [
            'version' => new DateInterval('P1D'),      // 版本缓存1天
            'server_info' => new DateInterval('PT30M'), // 服务器信息30分钟
            'access_token' => new DateInterval('PT50M'), // 访问token50分钟
            'refresh_token' => new DateInterval('P7D'),  // 刷新token7天
        ],
    ]
);
echo "✓ 自定义 TTL 配置完成\n\n";

// 5. 测试缓存功能
echo "5. 测试基础缓存功能:\n";
$cacheManager = $keycloak1->getCacheManager();

// 设置测试值
$cacheManager->set('test_key', 'test_value', new DateInterval('PT1H'));
$value = $cacheManager->getCache()->get('test_key');
echo "✓ 缓存读写测试: $value\n";

// 测试是否存在
if ($cacheManager->getCache()->has('test_key')) {
    echo "✓ 缓存键存在检查通过\n";
}

// 清理测试
$cacheManager->getCache()->delete('test_key');
echo "✓ 缓存清理完成\n\n";

echo "=== Symfony Cache 适配器可选项 ===\n";
echo "✓ ArrayAdapter - 内存缓存 (默认)\n";
echo "✓ FilesystemAdapter - 文件系统缓存\n";
echo "✓ ApcuAdapter - APCu 缓存 (需要 APCu 扩展)\n";
echo "✓ RedisAdapter - Redis 缓存 (需要 Redis)\n";
echo "✓ MemcachedAdapter - Memcached 缓存 (需要 Memcached)\n";
echo "✓ DoctrineDbalAdapter - 数据库缓存\n";
echo "✓ PdoAdapter - PDO 数据库缓存\n";
echo "✓ ChainAdapter - 多级缓存链\n\n";

echo "🎉 Symfony Cache 集成测试完成！\n";
