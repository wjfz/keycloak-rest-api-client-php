<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Resource;

use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Resource\AttackDetection;
use Overtrue\Keycloak\Type\Map;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AttackDetection::class)]
class AttackDetectionTest extends TestCase
{
    public function test_clear_attack_detection_for_all_users_in_realm(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/attack-detection/brute-force/users',
            Method::DELETE,
            [
                'realm' => 'realm',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $attackDetection = new AttackDetection(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );
        $attackDetection->clear('realm');
    }

    public function test_clear_attack_detection_for_single_user_in_realm(): void
    {
        $command = new Command(
            '/admin/realms/{realm}/attack-detection/brute-force/users/{userId}',
            Method::DELETE,
            [
                'realm' => 'realm',
                'userId' => 'userId',
            ],
        );

        $commandExecutor = $this->createMock(CommandExecutor::class);
        $commandExecutor->expects(static::once())
            ->method('executeCommand')
            ->with($command);

        $attackDetection = new AttackDetection(
            $commandExecutor,
            $this->createMock(QueryExecutor::class),
        );
        $attackDetection->clearUser('realm', 'userId');
    }

    public function test_get_attack_detection_for_single_user_in_realm(): void
    {
        $query = new Query(
            '/admin/realms/{realm}/attack-detection/brute-force/users/{userId}',
            Map::class,
            [
                'realm' => 'realm',
                'userId' => 'userId',
            ],
        );

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn(new Map);

        $attackDetection = new AttackDetection(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );
        $attackDetection->userStatus('realm', 'userId');
    }
}
