<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Collection\CredentialCollection;
use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Collection\RoleCollection;
use Overtrue\Keycloak\Collection\UserCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\User as UserRepresentation;
use Psr\Http\Message\ResponseInterface;

class Users extends Resource
{
    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function all(string $realm, Criteria|array|null $criteria = null): UserCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users',
                UserCollection::class,
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    public function get(string $realm, string $userId): UserRepresentation
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users/{userId}',
                UserRepresentation::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\User|array<string, mixed>  $user
     */
    public function create(string $realm, UserRepresentation|array $user): UserRepresentation
    {
        if (! $user instanceof UserRepresentation) {
            $user = UserRepresentation::from($user);
        }

        $response = $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users',
                Method::POST,
                [
                    'realm' => $realm,
                ],
                $user,
            ),
        );

        $userId = $this->getIdFromResponse($response);

        if ($userId === null) {
            throw new \RuntimeException('Could not extract user id from response');
        }

        return $this->get($realm, $userId);
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\User|array<string, mixed>  $updatedUser
     */
    public function update(string $realm, string $userId, UserRepresentation|array $updatedUser): UserRepresentation
    {
        if (! $updatedUser instanceof UserRepresentation) {
            $updatedUser = UserRepresentation::from($updatedUser);
        }

        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}',
                Method::PUT,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
                $updatedUser,
            ),
        );

        return $this->get($realm, $userId);
    }

    public function delete(string $realm, string $userId): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function search(string $realm, Criteria|array|null $criteria = null): UserCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users',
                UserCollection::class,
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    public function joinGroup(string $realm, string $userId, string $groupId): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/groups/{groupId}',
                Method::PUT,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ],
            ),
        );
    }

    public function leaveGroup(string $realm, string $userId, string $groupId): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/groups/{groupId}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                    'groupId' => $groupId,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function retrieveGroups(string $realm, string $userId, Criteria|array|null $criteria = null): GroupCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users/{userId}/groups',
                GroupCollection::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
                $criteria,
            ),
        );
    }

    public function retrieveRealmRoles(string $realm, string $userId): RoleCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
                RoleCollection::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    public function retrieveAvailableRealmRoles(string $realm, string $userId): RoleCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users/{userId}/role-mappings/realm/available',
                RoleCollection::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Collection\RoleCollection|array<array<string,mixed>|\Overtrue\Keycloak\Representation\Role>  $roles
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     * @throws \ReflectionException
     */
    public function addRealmRoles(string $realm, string $userId, RoleCollection|array $roles): ResponseInterface
    {
        if (! $roles instanceof RoleCollection) {
            $roles = new RoleCollection($roles);
        }

        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
                Method::POST,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
                $roles,
            ),
        );
    }

    public function removeRealmRoles(string $realm, string $userId, RoleCollection $roles): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/role-mappings/realm',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
                $roles,
            ),
        );
    }

    /**
     * @param  array<string>|null  $actions
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string,mixed>|null  $criteria
     */
    public function executeActionsEmail(string $realm, string $userId, ?array $actions = null, Criteria|array|null $criteria = null): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/execute-actions-email',
                Method::PUT,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
                $actions,
                $criteria,
            ),
        );
    }

    /**
     * @param string $realm
     * @param string $userId
     * @param string $provider
     * @return ResponseInterface
     */
    public function removeFederatedIdentity(string $realm, string $userId, string $provider): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/users/{userId}/federated-identity/{provider}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                    'provider' => $provider,
                ],
            ),
        );
    }

    public function credentials(string $realm, string $userId): CredentialCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/users/{userId}/credentials',
                CredentialCollection::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    public function getIdFromResponse(ResponseInterface $response): ?string
    {
        // Location: http://keycloak:8080/admin/realms/master/users/999a5022-e757-4f5f-ba0e-1d3ccd601c34
        $location = $response->getHeaderLine('Location');

        preg_match('~/users/(?<id>[a-z0-9\-]+)$~', $location, $matches);

        return $matches['id'] ?? null;
    }
}
