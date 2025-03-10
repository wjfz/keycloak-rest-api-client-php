<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Type\Map;
use Psr\Http\Message\ResponseInterface;

class AttackDetection extends Resource
{
    public function clear(string $realm): ResponseInterface
    {
        return $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/attack-detection/brute-force/users',
                Method::DELETE,
                [
                    'realm' => $realm,
                ],
            ),
        );
    }

    public function userStatus(string $realm, string $userId): Map
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/realms/{realm}/attack-detection/brute-force/users/{userId}',
                Map::class,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }

    public function clearUser(string $realm, string $userId): void
    {
        $this->commandExecutor->executeCommand(
            new Command(
                '/admin/realms/{realm}/attack-detection/brute-force/users/{userId}',
                Method::DELETE,
                [
                    'realm' => $realm,
                    'userId' => $userId,
                ],
            ),
        );
    }
}
