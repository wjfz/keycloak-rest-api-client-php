<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Integration;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class KeycloakTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function test_fetches_keycloak_version_before_resource_is_accessed_for_the_first_time(): void
    {
        $reflection = new ReflectionClass($this->getKeycloak());
        $version = $reflection->getProperty('version')->getValue($this->getKeycloak());

        static::assertNull($version);

        $this->getKeycloak()->realms();

        $version = $reflection->getProperty('version')->getValue($this->getKeycloak());
        static::assertIsString($version);
    }
}
