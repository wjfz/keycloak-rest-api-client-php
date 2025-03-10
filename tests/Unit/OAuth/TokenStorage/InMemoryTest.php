<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\OAuth\TokenStorage;

use Overtrue\Keycloak\OAuth\TokenStorage\InMemory;
use Overtrue\Keycloak\Test\Unit\TokenGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InMemory::class)]
class InMemoryTest extends TestCase
{
    use TokenGenerator;

    public function test_returns_no_access_token_if_not_previously_set(): void
    {
        static::assertNull((new InMemory)->retrieveAccessToken());
    }

    public function test_returns_no_refresh_token_if_not_previously_set(): void
    {
        static::assertNull((new InMemory)->retrieveAccessToken());
    }

    public function test_returns_access_token_if_previously_set(): void
    {
        $accessToken = $this->generateToken(new \DateTimeImmutable);

        $storage = new InMemory;
        $storage->storeAccessToken($accessToken);

        static::assertSame($accessToken, $storage->retrieveAccessToken());
    }

    public function test_returns_refresh_token_if_previously_set(): void
    {
        $refreshToken = $this->generateToken(new \DateTimeImmutable);

        $storage = new InMemory;
        $storage->storeRefreshToken($refreshToken);

        static::assertSame($refreshToken, $storage->retrieveRefreshToken());
    }

    public function test_overrides_previously_stored_access_token(): void
    {
        $storedAccessToken = $this->generateToken(new \DateTimeImmutable);
        $newAccessToken = $this->generateToken(new \DateTimeImmutable);

        $storage = new InMemory;
        $storage->storeAccessToken($storedAccessToken);

        static::assertSame($storedAccessToken, $storage->retrieveAccessToken());

        $storage->storeAccessToken($newAccessToken);

        static::assertNotSame($storedAccessToken, $storage->retrieveAccessToken());
        static::assertSame($newAccessToken, $storage->retrieveAccessToken());
    }

    public function test_overrides_previously_stored_refresh_token(): void
    {
        $storedRefreshToken = $this->generateToken(new \DateTimeImmutable);
        $newRefreshToken = $this->generateToken(new \DateTimeImmutable);

        $storage = new InMemory;
        $storage->storeRefreshToken($storedRefreshToken);

        static::assertSame($storedRefreshToken, $storage->retrieveRefreshToken());

        $storage->storeRefreshToken($newRefreshToken);

        static::assertNotSame($storedRefreshToken, $storage->retrieveRefreshToken());
        static::assertSame($newRefreshToken, $storage->retrieveRefreshToken());
    }
}
