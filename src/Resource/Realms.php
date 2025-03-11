<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\KeysMetadata;
use Overtrue\Keycloak\Representation\Realm;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-type AdminEvent array<mixed>
 */
class Realms extends Resource
{
    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function all(Criteria|array|null $criteria = null): RealmCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms',
                RealmCollection::class,
                criteria: $criteria,
            ),
        );
    }

    public function get(string $realm): Realm
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}',
                Realm::class,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Realm|array<string,mixed>  $realm
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function import(Realm|array $realm): Realm
    {
        if (! $realm instanceof Realm) {
            $realm = Realm::from($realm);
        }

        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms',
                Method::POST,
                payload: $realm,
            ),
        );

        return $this->get(realm: $realm->getRealm());
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Realm|array<string,string>  $updatedRealm
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function update(string $realm, Realm|array $updatedRealm): Realm
    {
        if (! $updatedRealm instanceof Realm) {
            $updatedRealm = Realm::from($updatedRealm);
        }

        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}',
                Method::PUT,
                [
                    'realm' => $realm,
                ],
                $updatedRealm,
            ),
        );

        return $this->get($updatedRealm->getRealm());
    }

    public function delete(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}',
                Method::DELETE,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     * @return array<array-key, mixed>
     */
    public function adminEvents(string $realm, Criteria|array|null $criteria = null): array
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/admin-events',
                'array',
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function keys(string $realm, Criteria|array|null $criteria = null): KeysMetadata
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/keys',
                KeysMetadata::class,
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    public function deleteAdminEvents(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/admin-events',
                Method::DELETE,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    public function clearKeysCache(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/clear-keys-cache',
                Method::POST,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    public function clearRealmCache(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/clear-realm-cache',
                Method::POST,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    public function clearUserCache(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/clear-user-cache',
                Method::POST,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }
}
