<?php

declare(strict_types=1);

namespace Overtrue\Keycloak;

use DateInterval;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Overtrue\Keycloak\Cache\CacheManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Overtrue\Keycloak\Http\Client;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Resource\AttackDetection;
use Overtrue\Keycloak\Resource\Clients;
use Overtrue\Keycloak\Resource\Groups;
use Overtrue\Keycloak\Resource\Organizations;
use Overtrue\Keycloak\Resource\Realms;
use Overtrue\Keycloak\Resource\Resource;
use Overtrue\Keycloak\Resource\Roles;
use Overtrue\Keycloak\Resource\ServerInfo;
use Overtrue\Keycloak\Resource\Users;
use Overtrue\Keycloak\Serializer\Serializer;
use Psr\SimpleCache\CacheInterface;

/**
 * @codeCoverageIgnore
 */
class Keycloak
{
    private ?string $version = null;

    private Client $client;

    private Serializer $serializer;

    private CommandExecutor $commandExecutor;

    private QueryExecutor $queryExecutor;

    private CacheManager $cacheManager;

    /**
     * @param string $baseUrl
     * @param string $username
     * @param string $password
     * @param ClientInterface|null $guzzleClient
     * @param CacheInterface|null $cache
     * @param array<string, mixed> $cacheConfig
     * @phpstan-param array<string, mixed> $cacheConfig
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $username,
        private readonly string $password,
        ?ClientInterface $guzzleClient = null,
        ?CacheInterface $cache = null,
        array $cacheConfig = []
    ) {
        // Initialize cache - use Symfony Cache ArrayAdapter as default implementation
        $cache = $cache ?? new Psr16Cache(new ArrayAdapter());

        // Merge default configuration with user configuration
        $mergedConfig = array_merge([
            'ttl' => [
                'version' => new DateInterval('PT24H'),       // Version cache for 24 hours
                'server_info' => new DateInterval('PT1H'),    // Server info cache for 1 hour
                'access_token' => new DateInterval('PT1H'),   // Access token cache for 1 hour
                'refresh_token' => new DateInterval('P1D'),   // Refresh token cache for 1 day
            ]
        ], $cacheConfig);

        // Get prefix configuration, default to 'keycloak_'
        $prefix = $mergedConfig['prefix'] ?? 'keycloak_';

        $this->cacheManager = new CacheManager($cache, $mergedConfig, $prefix);

        $guzzleClient = $guzzleClient ?? new GuzzleClient();

        $this->client = new Client($this, $guzzleClient, $this->cacheManager);
        $this->serializer = new Serializer($this->version);
        $this->commandExecutor = new CommandExecutor($this->client, $this->serializer);
        $this->queryExecutor = new QueryExecutor($this->client, $this->serializer);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getVersion(): string
    {
        $this->fetchVersion();

        return $this->version;
    }

    public function attackDetection(): AttackDetection
    {
        $this->fetchVersion();

        return new AttackDetection($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function serverInfo(): ServerInfo
    {
        return new ServerInfo($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function realms(): Realms
    {
        $this->fetchVersion();

        return new Realms($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function clients(): Clients
    {
        $this->fetchVersion();

        return new Clients($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function users(): Users
    {
        $this->fetchVersion();

        return new Users($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function groups(): Groups
    {
        $this->fetchVersion();

        return new Groups($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function roles(): Roles
    {
        $this->fetchVersion();

        return new Roles($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    public function organizations(): Organizations
    {
        $this->fetchVersion();

        return new Organizations($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }

    /**
     * @template T of Resource
     *
     * @param  class-string<T>  $resource
     * @return T
     */
    public function resource(string $resource): Resource
    {
        $this->fetchVersion();

        return new $resource($this->commandExecutor, $this->queryExecutor, $this->cacheManager);
    }



    /**
     * Clear version cache
     */
    public function clearVersionCache(): bool
    {
        $cleared = $this->cacheManager->delete('version');
        $this->version = null; // Reset version in memory
        return $cleared;
    }



    private function fetchVersion(): void
    {
        if ($this->version) {
            return;
        }

        // Use cache to get version information
        $cacheKey = 'version';
        $this->version = $this->cacheManager->get($cacheKey, function() {
            return $this->serverInfo()->get()->getSystemInfo()->getVersion();
        }, $this->cacheManager->getTtl('version', new DateInterval('PT24H')));

        $this->serializer = new Serializer($this->version);
        $this->commandExecutor = new CommandExecutor($this->client, $this->serializer);
        $this->queryExecutor = new QueryExecutor($this->client, $this->serializer);
    }
}
