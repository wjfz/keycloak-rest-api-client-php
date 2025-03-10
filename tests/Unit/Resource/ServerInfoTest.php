<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Resource;

use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Representation\ServerInfo as ServerInfoRepresentation;
use Overtrue\Keycloak\Resource\ServerInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ServerInfo::class)]
class ServerInfoTest extends TestCase
{
    public function test_get_server_info(): void
    {
        $query = new Query(
            '/admin/serverinfo',
            ServerInfoRepresentation::class,
        );

        $serverInfoRepresentation = new ServerInfoRepresentation;

        $queryExecutor = $this->createMock(QueryExecutor::class);
        $queryExecutor->expects(static::once())
            ->method('executeQuery')
            ->with($query)
            ->willReturn($serverInfoRepresentation);

        $serverInfo = new ServerInfo(
            $this->createMock(CommandExecutor::class),
            $queryExecutor,
        );

        static::assertSame(
            $serverInfoRepresentation,
            $serverInfo->get(),
        );
    }
}
