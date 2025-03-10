<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Collection\RoleCollection;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\Role;
use Psr\Http\Message\ResponseInterface;

class Roles extends Resource
{
    public function all(string $realm, ?Criteria $criteria = null): RoleCollection
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/roles',
                RoleCollection::class,
                [
                    'realm' => $realm,
                ],
                $criteria,
            ),
        );
    }

    public function get(string $realm, string $roleName): Role
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/roles/{roleName}',
                Role::class,
                [
                    'realm' => $realm,
                    'roleName' => $roleName,
                ],
            ),
        );
    }

    public function create(string $realm, Role $role): Role
    {
        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/roles',
                Method::POST,
                [
                    'realm' => $realm,
                ],
                $role,
            ),
        );

        return $this->get($realm, $role->getName());
    }

    public function delete(string $realm, string $roleName): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/roles/{roleName}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'roleName' => $roleName,
                ],
            ),
        );
    }

    public function update(string $realm, Role $role): Role
    {
        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/roles/{roleName}',
                Method::PUT,
                [
                    'realm' => $realm,
                    'roleName' => $role->getName(),
                ],
                $role,
            ),
        );

        return $this->get($realm, $role->getName());
    }
}
