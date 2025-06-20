<?php

declare(strict_types=1);

namespace Overtrue\Keycloak;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Overtrue\Keycloak\Http\Client;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\OAuth\TokenStorage\InMemory;
use Overtrue\Keycloak\OAuth\TokenStorageInterface;
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

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $username,
        private readonly string $password,
        private readonly TokenStorageInterface $tokenStorage = new InMemory,
        ?ClientInterface $guzzleClient = new GuzzleClient,
    ) {
        $this->client = new Client($this, $guzzleClient, $this->tokenStorage);
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

        return new AttackDetection($this->commandExecutor, $this->queryExecutor);
    }

    public function serverInfo(): ServerInfo
    {
        return new ServerInfo($this->commandExecutor, $this->queryExecutor);
    }

    public function realms(): Realms
    {
        $this->fetchVersion();

        return new Realms($this->commandExecutor, $this->queryExecutor);
    }

    public function clients(): Clients
    {
        $this->fetchVersion();

        return new Clients($this->commandExecutor, $this->queryExecutor);
    }

    public function users(): Users
    {
        $this->fetchVersion();

        return new Users($this->commandExecutor, $this->queryExecutor);
    }

    public function groups(): Groups
    {
        $this->fetchVersion();

        return new Groups($this->commandExecutor, $this->queryExecutor);
    }

    public function roles(): Roles
    {
        $this->fetchVersion();

        return new Roles($this->commandExecutor, $this->queryExecutor);
    }

    public function organizations(): Organizations
    {
        $this->fetchVersion();

        return new Organizations($this->commandExecutor, $this->queryExecutor);
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

        return new $resource($this->commandExecutor, $this->queryExecutor);
    }

    private function fetchVersion(): void
    {
        if ($this->version) {
            return;
        }

        $this->version = $this->serverInfo()->get()->getSystemInfo()->getVersion();
        $this->serializer = new Serializer($this->version);
        $this->commandExecutor = new CommandExecutor($this->client, $this->serializer);
    }
}
