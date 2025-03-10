<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Integration\Resource;

use Overtrue\Keycloak\Representation\ServerInfo;
use Overtrue\Keycloak\Test\Integration\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class ServerInfoTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function test_can_get_server_info(): void
    {
        $serverInfo = $this->getKeycloak()->serverInfo()->get();

        static::assertInstanceOf(ServerInfo::class, $serverInfo);
    }
}
