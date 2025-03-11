<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Collection\UserCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\Group;
use Psr\Http\Message\ResponseInterface;

class Groups extends Resource
{
    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string,string>|null  $criteria
     */
    public function all(string $realm, Criteria|array|null $criteria = null): GroupCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/groups',
                GroupCollection::class,
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    public function byPath(string $realm, string $path = ''): Group
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/group-by-path/{path}',
                Group::class,
                [
                    'realm' => $realm,
                    'path' => $path,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function children(string $realm, string $groupId, Criteria|array|null $criteria = null): GroupCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/groups/{groupId}/children',
                GroupCollection::class,
                [
                    'realm' => $realm,
                    'groupId' => $groupId,
                ],
                $criteria,
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string, string>|null  $criteria
     */
    public function members(string $realm, string $groupId, Criteria|array|null $criteria = null): UserCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/groups/{groupId}/members',
                UserCollection::class,
                [
                    'realm' => $realm,
                    'groupId' => $groupId,
                ],
                $criteria,
            ),
        );
    }

    public function get(string $realm, string $groupId): Group
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/groups/{groupId}',
                Group::class,
                [
                    'realm' => $realm,
                    'groupId' => $groupId,
                ],
            ),
        );
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Group|array<string,mixed>  $group
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function create(string $realm, Group|array $group): Group
    {
        if (! $group instanceof Group) {
            $group = Group::from($group);
        }

        $response = $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/groups',
                Method::POST,
                [
                    'realm' => $realm,
                ],
                $group,
            ),
        );

        $groupId = $this->getIdFromResponse($response);

        if ($groupId === null) {
            throw new \RuntimeException('Could not extract group id from response');
        }

        return $this->get($realm, $groupId);
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Group|array<string,mixed>  $group
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function createChild(string $realm, Group|array $group, string $parentGroupId): Group
    {
        if (! $group instanceof Group) {
            $group = Group::from($group);
        }

        $response = $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/groups/{groupId}/children',
                Method::POST,
                [
                    'realm' => $realm,
                    'groupId' => $parentGroupId,
                ],
                $group,
            ),
        );

        $groupId = $this->getIdFromResponse($response);

        if ($groupId === null) {
            throw new \RuntimeException('Could not extract group id from response');
        }

        return $this->get($realm, $groupId);
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Group|array<string,mixed>  $updatedGroup
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function update(string $realm, string $groupId, Group|array $updatedGroup): ResponseInterface
    {
        if (! $updatedGroup instanceof Group) {
            $updatedGroup = Group::from($updatedGroup);
        }

        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/groups/{groupId}',
                Method::PUT,
                [
                    'realm' => $realm,
                    'groupId' => $groupId,
                ],
                $updatedGroup,
            ),
        );
    }

    public function delete(string $realm, string $groupId): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/groups/{groupId}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'groupId' => $groupId,
                ],
            ),
        );
    }

    public function getIdFromResponse(ResponseInterface $response): ?string
    {
        // Location: http://keycloak:8080/admin/realms/{realm}/groups/1ccce35d-eeac-4eb7-90ec-268abc98c864
        $location = $response->getHeaderLine('Location');

        preg_match('~/groups/(?<id>[a-z0-9\-]+)$~', $location, $matches);

        return $matches['id'] ?? null;
    }
}
