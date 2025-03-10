<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Resource;

use GuzzleHttp\Psr7\Response;
use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Representation\KeysMetadata;
use Overtrue\Keycloak\Representation\Realm;
use Overtrue\Keycloak\Resource\Realms;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Realms::class)]
class RealmsTest extends TestCase
{
    public function test_get_all_realms(): void
    {
        $query = new Query(
            '/admin/realms',
            RealmCollection::class,
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(
                new RealmCollection([
                    new Realm(realm: 'realm-1'),
                    new Realm(realm: 'realm-2'),
                ]),
            );

        $realms = new Realms(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );
        $realms = $realms->all();

        static::assertInstanceOf(RealmCollection::class, $realms);
        static::assertCount(2, $realms);
    }

    public function test_import_realm(): void
    {
        $command = new Command(
            '/admin/realms',
            Method::POST,
            [],
            new Realm(realm: 'imported-realm'),
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $query = new Query(
            '/admin/realms/{realm}',
            Realm::class,
            [
                'realm' => 'imported-realm',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(
                new Realm(realm: 'imported-realm'),
            );

        $realms = new Realms(
            $commandExecutor,
            $queryExecutor,
        );
        $realm = $realms->import(new Realm(realm: 'imported-realm'));

        static::assertSame('imported-realm', $realm->getRealm());
    }

    public function test_update_realm(): void
    {
        $updatedRealm = new Realm(realm: 'updated-realm', displayName: 'Updated Realm');

        $command = new Command(
            '/admin/realms/{realm}',
            Method::PUT,
            [
                'realm' => 'to-be-updated-realm',
            ],
            $updatedRealm,
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $query = new Query(
            '/admin/realms/{realm}',
            Realm::class,
            [
                'realm' => 'updated-realm',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(
                new Realm(
                    displayName: 'Updated Realm',
                    realm: 'updated-realm',
                ),
            );

        $realms = new Realms(
            $commandExecutor,
            $queryExecutor,
        );
        $realm = $realms->update('to-be-updated-realm', $updatedRealm);

        static::assertSame('Updated Realm', $realm->getDisplayName());
    }

    public function test_delete_realm(): void
    {
        $command = new Command(
            '/admin/realms/{realm}',
            Method::DELETE,
            [
                'realm' => 'to-be-deleted-realm',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $realms = new Realms(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $realms->delete('to-be-deleted-realm');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_get_admin_events(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/admin-events',
            'array',
            [
                'realm' => 'realm-with-admin-events',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn([
                [], [],
            ]);

        $realms = new Realms(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );
        $adminEvents = $realms->adminEvents('realm-with-admin-events');

        static::assertCount(2, $adminEvents);
    }

    public function test_delete_admin_events(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/admin-events',
            Method::DELETE,
            [
                'realm' => 'realm-with-admin-events',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $realms = new Realms(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $realms->deleteAdminEvents('realm-with-admin-events');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_clear_keys_cache(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/clear-keys-cache',
            Method::POST,
            [
                'realm' => 'realm-with-cache',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $realms = new Realms(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $realms->clearKeysCache('realm-with-cache');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_clear_realm_cache(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/clear-realm-cache',
            Method::POST,
            [
                'realm' => 'realm-with-cache',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $realms = new Realms(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $realms->clearRealmCache('realm-with-cache');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_clear_user_cache(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/clear-user-cache',
            Method::POST,
            [
                'realm' => 'realm-with-cache',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command)
            ->willReturn(new Response(204));

        $realms = new Realms(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );

        $response = $realms->clearUserCache('realm-with-cache');

        static::assertSame(204, $response->getStatusCode());
    }

    public function test_get_keys(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/keys',
            KeysMetadata::class,
            [
                'realm' => 'realm-with-keys',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(new KeysMetadata);

        $realms = new Realms(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        $keys = $realms->keys('realm-with-keys');

        static::assertInstanceOf(KeysMetadata::class, $keys);
    }
}
