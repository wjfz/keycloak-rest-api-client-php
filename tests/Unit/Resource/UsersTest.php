<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Resource;

use GuzzleHttp\Psr7\Response;
use Overtrue\Keycloak\Collection\CredentialCollection;
use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Collection\RoleCollection;
use Overtrue\Keycloak\Collection\UserCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Representation\Group;
use Overtrue\Keycloak\Representation\Role;
use Overtrue\Keycloak\Representation\User;
use Overtrue\Keycloak\Resource\Users;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Users::class)]
class UsersTest extends TestCase
{
    public function test_get_all_users(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users',
            UserCollection::class,
            [
                'realm' => 'test-realm',
            ],
        );

        $clientCollection = new UserCollection([
            new User(id: 'test-user-1'),
            new User(id: 'test-user-2'),
        ]);

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($clientCollection);

        $clients = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $clientCollection,
            $clients->all('test-realm'),
        );
    }

    public function test_get_user(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users/{userId}',
            User::class,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
        );

        $client = new User(id: 'test-user-1');

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($client);

        $clients = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $client,
            $clients->get('test-realm', 'test-user'),
        );
    }

    public function test_create_user(): void
    {
        $createdUser = new User(id: 'uuid', username: 'imported-user');

        $command = new Command(
            '/admin/realms/{realm}/users',
            Method::POST,
            [
                'realm' => 'test-realm',
            ],
            $createdUser,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(201, [
                'Location' => '/admin/realms/test-realm/users/uuid',
            ]));

        $users = $this->getMockBuilder(Users::class)
            ->setConstructorArgs([
                $commandExecutor,
                $this->createMock(QueryExecutor::class),
            ])
            ->onlyMethods(['get'])
            ->getMock();
        $users->expects(static::once())
            ->method('get')
            ->with('test-realm', 'uuid')
            ->willReturn($createdUser);

        $user = $users->create('test-realm', $createdUser);

        static::assertSame('uuid', $user->getId());
    }

    public function test_delete_user(): void
    {
        $deletedUser = new User(id: 'deleted-user');
        $deletedUserId = $deletedUser->getId();

        static::assertIsString($deletedUserId);

        $command = new Command(
            '/admin/realms/{realm}/users/{userId}',
            Method::DELETE,
            [
                'realm' => 'test-realm',
                'userId' => $deletedUserId,
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->delete('test-realm', $deletedUserId);

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_update_user(): void
    {
        $updatedUser = new User(id: 'test-user', username: 'new-username');

        $command = new Command(
            '/admin/realms/{realm}/users/{userId}',
            Method::PUT,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
            $updatedUser,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with(new Query(
                '/admin/realms/{realm}/users/{userId}',
                User::class,
                [
                    'realm' => 'test-realm',
                    'userId' => 'test-user',
                ],
            ))
            ->willReturn($updatedUser);

        $users = new Users(
            $commandExecutor,
            $queryExecutor,
        );

        $user = $users->update('test-realm', 'test-user', $updatedUser);

        static::assertSame('new-username', $user->getUsername());
    }

    public function test_search_user(): void
    {
        $criteria = new Criteria([
            'username' => 'test-user',
            'exact' => true,
        ]);

        $query = new Query(
            '/admin/realms/{realm}/users',
            UserCollection::class,
            [
                'realm' => 'test-realm',
            ],
            $criteria,
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(new UserCollection);

        $users = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        $users = $users->search('test-realm', $criteria);

        static::assertInstanceOf(UserCollection::class, $users);
    }

    public function test_join_group(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/groups/{groupId}',
            Method::PUT,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
                'groupId' => 'test-group',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->joinGroup('test-realm', 'test-user', 'test-group');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_leave_group(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/groups/{groupId}',
            Method::DELETE,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
                'groupId' => 'test-group',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->leaveGroup('test-realm', 'test-user', 'test-group');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_retrieve_groups(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users/{userId}/groups',
            GroupCollection::class,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
        );

        $groupCollection = new GroupCollection([
            new Group(id: 'test-group-1'),
            new Group(id: 'test-group-2'),
        ]);

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($groupCollection);

        $users = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $groupCollection,
            $users->retrieveGroups('test-realm', 'test-user'),
        );
    }

    public function test_retrieve_realm_roles(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
            RoleCollection::class,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
        );

        $roleCollection = new RoleCollection([
            new Role(id: 'test-role-1'),
            new Role(id: 'test-role-2'),
        ]);

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($roleCollection);

        $users = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $roleCollection,
            $users->retrieveRealmRoles('test-realm', 'test-user'),
        );
    }

    public function test_retrieve_available_realm_roles(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users/{userId}/role-mappings/realm/available',
            RoleCollection::class,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
        );

        $roleCollection = new RoleCollection([
            new Role(id: 'test-role-1'),
            new Role(id: 'test-role-2'),
        ]);

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($roleCollection);

        $users = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $roleCollection,
            $users->retrieveAvailableRealmRoles('test-realm', 'test-user'),
        );
    }

    public function test_add_realm_roles(): void
    {
        $roles = new RoleCollection([new Role(id: 'uuid', name: 'some-name')]);

        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
            Method::POST,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
            $roles,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->addRealmRoles('test-realm', 'test-user', $roles);

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_remove_realm_roles(): void
    {
        $roles = new RoleCollection([new Role(id: 'uuid', name: 'some-name')]);

        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
            Method::DELETE,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
            ],
            $roles,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->removeRealmRoles('test-realm', 'test-user', $roles);

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_execute_actions_email(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/execute-actions-email',
            Method::PUT,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user-id',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->executeActionsEmail('test-realm', 'test-user-id');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_remove_user_federated_identity(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/users/{userId}/federated-identity/{provider}',
            Method::DELETE,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user',
                'provider' => 'test-provider',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $users->removeFederatedIdentity('test-realm', 'test-user', 'test-provider');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_credentials(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/users/{userId}/credentials',
            CredentialCollection::class,
            [
                'realm' => 'test-realm',
                'userId' => 'test-user-id',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(new CredentialCollection);

        $users = new Users(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        $credentials = $users->credentials('test-realm', 'test-user-id');

        static::assertInstanceOf(CredentialCollection::class, $credentials);
    }

    public function test_create_user_without_response_location(): void
    {
        $createdUser = new User(id: 'uuid', username: 'imported-user');

        $command = new Command(
            '/admin/realms/{realm}/users',
            Method::POST,
            [
                'realm' => 'test-realm',
            ],
            $createdUser,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(201, [
                // 'Location' => '/admin/realms/test-realm/users/uuid',
            ]));

        $users = new Users(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        self::expectExceptionMessage('Could not extract user id from response');

        $users->create('test-realm', $createdUser);

        static::fail('Expected exception not thrown');
    }
}
