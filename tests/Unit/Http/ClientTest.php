<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use DateTimeImmutable;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Overtrue\Keycloak\Cache\CacheManager;
use Overtrue\Keycloak\Http\Client;
use Overtrue\Keycloak\Keycloak;
use Overtrue\Keycloak\Test\Unit\TokenGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(Client::class)]
class ClientTest extends TestCase
{
    use TokenGenerator;

    private Keycloak $keycloak;

    private CacheManager $cacheManager;

    protected function setUp(): void
    {
        $this->keycloak = new Keycloak(
            'http://keycloak:8080',
            'admin',
            'admin',
        );

        // 使用反射获取 Keycloak 实例内部的 CacheManager
        $reflection = new ReflectionClass($this->keycloak);
        $property = $reflection->getProperty('cacheManager');
        $property->setAccessible(true);
        $this->cacheManager = $property->getValue($this->keycloak);
    }

    public function test_authorizes_before_sending_request(): void
    {
        $accessToken = $this->generateToken((new DateTimeImmutable)->modify('+1 hour'));
        $refreshToken = $this->generateToken((new DateTimeImmutable)->modify('+1 hour'));

        $authorizationResponse = new Response(
            status: 200,
            body: json_encode(
                value: [
                    'access_token' => $accessToken->toString(),
                    'refresh_token' => $refreshToken->toString(),
                ],
                flags: JSON_THROW_ON_ERROR,
            ),
        );

        $realmsResponse = new Response(
            status: 200,
            body: json_encode(
                value: [
                    'realms' => [],
                ],
                flags: JSON_THROW_ON_ERROR,
            ),
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects(static::exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $authorizationResponse,
                $realmsResponse,
            );

        $client = new Client($this->keycloak, $httpClient, $this->cacheManager);
        $client->request('GET', '/admin/realms');

        static::assertTrue($client->isAuthorized());
    }

    public function test_is_not_authorized_if_cache_contains_no_access_token(): void
    {
        $client = new Client(
            $this->keycloak,
            $this->createMock(ClientInterface::class),
            $this->cacheManager,
        );

        static::assertFalse($client->isAuthorized());
    }

    public function test_is_not_authorized_if_cache_contains_expired_access_token(): void
    {
        $accessToken = $this->generateToken((new DateTimeImmutable)->modify('-1 hour'));

        // 直接存储过期的token到缓存
        $this->cacheManager->set('access_token', $accessToken->toString());

        $client = new Client(
            $this->keycloak,
            $this->createMock(ClientInterface::class),
            $this->cacheManager,
        );

        static::assertFalse($client->isAuthorized());
    }

    public function test_is_authorized_if_cache_contains_unexpired_access_token(): void
    {
        $accessToken = $this->generateToken((new DateTimeImmutable)->modify('+1 hour'));

        // 直接存储未过期的token到缓存
        $this->cacheManager->set('access_token', $accessToken->toString());

        $client = new Client(
            $this->keycloak,
            $this->createMock(ClientInterface::class),
            $this->cacheManager,
        );

        static::assertTrue($client->isAuthorized());
    }

    public function test_can_clear_tokens(): void
    {
        $accessToken = $this->generateToken((new DateTimeImmutable)->modify('+1 hour'));
        $refreshToken = $this->generateToken((new DateTimeImmutable)->modify('+1 hour'));

        // 存储tokens到缓存
        $this->cacheManager->set('access_token', $accessToken->toString());
        $this->cacheManager->set('refresh_token', $refreshToken->toString());

        $client = new Client(
            $this->keycloak,
            $this->createMock(ClientInterface::class),
            $this->cacheManager,
        );

        // 验证tokens存在
        static::assertTrue($this->cacheManager->has('access_token'));
        static::assertTrue($this->cacheManager->has('refresh_token'));

        // 清除tokens
        $result = $client->clearTokens();

        // 验证清除成功
        static::assertTrue($result);
        static::assertFalse($this->cacheManager->has('access_token'));
        static::assertFalse($this->cacheManager->has('refresh_token'));
    }
}
