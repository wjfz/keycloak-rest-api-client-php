<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Resource;

use GuzzleHttp\Psr7\Response;
use Overtrue\Keycloak\Collection\ClientCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Representation\Client as ClientRepresentation;
use Overtrue\Keycloak\Representation\Credential;
use Overtrue\Keycloak\Resource\Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Clients::class)]
class ClientsTest extends TestCase
{
    public function test_get_all_clients(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/clients',
            ClientCollection::class,
            [
                'realm' => 'test-realm',
            ],
        );

        $clientCollection = new ClientCollection([
            new ClientRepresentation(clientId: 'test-client-1'),
            new ClientRepresentation(clientId: 'test-client-2'),
        ]);

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($clientCollection);

        $clients = new Clients(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $clientCollection,
            $clients->all('test-realm'),
        );
    }

    public function test_get_client(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/clients/{clientUuid}',
            ClientRepresentation::class,
            [
                'realm' => 'test-realm',
                'clientUuid' => 'test-client',
            ],
        );

        $client = new ClientRepresentation(clientId: 'test-client-1');

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($client);

        $clients = new Clients(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $client,
            $clients->get('test-realm', 'test-client'),
        );
    }

    public function test_update_client(): void
    {
        $updatedClient = new ClientRepresentation(clientId: 'updated-client', id: 'uuid');
        $updatedClientId = $updatedClient->getId();

        static::assertIsString($updatedClientId);

        $command = new Command(
            '/admin/realms/{realm}/clients/{clientUuid}',
            Method::PUT,
            [
                'realm' => 'test-realm',
                'clientUuid' => 'test-client',
            ],
            $updatedClient,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $query = new Query(
            '/admin/realms/{realm}/clients/{clientUuid}',
            ClientRepresentation::class,
            [
                'realm' => 'test-realm',
                'clientUuid' => $updatedClientId,
            ],
        );

        $client = new ClientRepresentation(clientId: 'updated-client');

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($client);

        $clients = new Clients(
            $commandExecutor,
            $queryExecutor,
        );

        static::assertSame(
            $client,
            $clients->update('test-realm', 'test-client', $updatedClient),
        );
    }

    public function test_import_client(): void
    {
        $importedClient = new ClientRepresentation(clientId: 'imported-client', id: 'uuid');
        $importedClientId = $importedClient->getId();

        static::assertIsString($importedClientId);

        $command = new Command(
            '/admin/realms/{realm}/clients',
            Method::POST,
            [
                'realm' => 'test-realm',
            ],
            $importedClient,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $query = new Query(
            '/admin/realms/{realm}/clients/{clientUuid}',
            ClientRepresentation::class,
            [
                'realm' => 'test-realm',
                'clientUuid' => $importedClientId,
            ],
        );

        $client = new ClientRepresentation(clientId: 'updated-client');

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($client);

        $clients = new Clients(
            $commandExecutor,
            $queryExecutor,
        );

        static::assertSame(
            $client,
            $clients->import('test-realm', $importedClient),
        );
    }

    public function test_delete_client(): void
    {
        $deletedClient = new ClientRepresentation(clientId: 'deleted-client', id: 'uuid');
        $deletedClientId = $deletedClient->getId();

        static::assertIsString($deletedClientId);

        $command = new Command(
            '/admin/realms/{realm}/clients/{clientUuid}',
            Method::DELETE,
            [
                'realm' => 'test-realm',
                'clientUuid' => $deletedClientId,
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $clients = new Clients(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $clients->delete('test-realm', $deletedClientId);

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_get_user_sessions(): void
    {
        $client = new ClientRepresentation(id: 'test-client');
        $clientId = $client->getId();

        static::assertIsString($clientId);

        $query = new Query(
            '/admin/realms/{realm}/clients/{clientUuid}/user-sessions',
            'array',
            [
                'realm' => 'test-realm',
                'clientUuid' => $clientId,
            ],
        );

        $userSessions = [];

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($userSessions);

        $clients = new Clients(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $userSessions,
            $clients->getUserSessions('test-realm', $clientId),
        );
    }

    public function test_get_client_secret(): void
    {
        $client = new ClientRepresentation(id: 'test-client');
        $clientUuid = $client->getId();

        static::assertIsString($clientUuid);

        $credential = new Credential;

        $query = new Query(
            '/admin/realms/{realm}/clients/{clientUuid}/client-secret',
            Credential::class,
            [
                'realm' => 'test-realm',
                'clientUuid' => $clientUuid,
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($credential);

        $clients = new Clients(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $credential,
            $clients->getClientSecret('test-realm', $clientUuid),
        );
    }
}
