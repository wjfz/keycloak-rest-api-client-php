<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

use DateInterval;
use DateTime;
use GuzzleHttp\ClientInterface;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;
use Overtrue\Keycloak\Cache\CacheManager;
use Overtrue\Keycloak\Keycloak;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private const ACCESS_TOKEN_KEY = 'access_token';

    private const REFRESH_TOKEN_KEY = 'refresh_token';

    public function __construct(
        private Keycloak $keycloak,
        private ClientInterface $httpClient,
        private CacheManager $cacheManager,
    ) {}

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function request(string $method, string $path = '', array $options = []): ResponseInterface
    {
        if (! $this->isAuthorized()) {
            $this->authorize();
        }

        $accessToken = $this->retrieveAccessToken();
        $defaultOptions = [
            'base_uri' => $this->keycloak->getBaseUrl(),
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->toString(),
            ],
        ];

        $options = array_merge_recursive($options, $defaultOptions);

        return $this->httpClient->request(
            $method,
            $this->keycloak->getBaseUrl().$path,
            $options,
        );
    }

    public function isAuthorized(): bool
    {
        return $this->retrieveAccessToken()?->isExpired(new DateTime) === false;
    }

    /**
     * @throws \JsonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authorize(): void
    {
        $tokens = $this->fetchTokens();
        $parser = (new Token\Parser(new JoseEncoder));

        $this->storeAccessToken($parser->parse($tokens['access_token']));
        $this->storeRefreshToken($parser->parse($tokens['refresh_token']));
    }

    /**
     * @return array{access_token: non-empty-string, refresh_token: non-empty-string}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function fetchTokens(): array
    {
        $refreshToken = $this->retrieveRefreshToken();

        // If refresh token exists and is not expired, use refresh token to get new tokens first
        if ($refreshToken !== null && ! $refreshToken->isExpired(new DateTime)) {
            try {
                $response = $this->httpClient->request(
                    'POST',
                    $this->keycloak->getBaseUrl().'/realms/master/protocol/openid-connect/token',
                    [
                        'form_params' => [
                            'refresh_token' => $refreshToken->toString(),
                            'client_id' => 'admin-cli',
                            'grant_type' => 'refresh_token',
                        ],
                    ],
                );
            } catch (\Throwable $e) {
                $response = $this->fetchTokensWithPassword();
            }
        } else {
            $response = $this->fetchTokensWithPassword();
        }

        $tokens = json_decode(
            $response->getBody()->getContents(),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ];
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchTokensWithPassword(): ResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            $this->keycloak->getBaseUrl().'/realms/master/protocol/openid-connect/token',
            [
                'form_params' => [
                    'username' => $this->keycloak->getUsername(),
                    'password' => $this->keycloak->getPassword(),
                    'client_id' => 'admin-cli',
                    'grant_type' => 'password',
                ],
            ],
        );
    }

    /**
     * Store access token
     */
    private function storeAccessToken(Token $accessToken): void
    {
        $this->cacheManager->set(
            self::ACCESS_TOKEN_KEY,
            $accessToken->toString(),
            $this->cacheManager->getTtl('access_token', new DateInterval('PT1H'))
        );
    }

    /**
     * Store refresh token
     */
    private function storeRefreshToken(Token $refreshToken): void
    {
        $this->cacheManager->set(
            self::REFRESH_TOKEN_KEY,
            $refreshToken->toString(),
            $this->cacheManager->getTtl('refresh_token', new DateInterval('P1D'))
        );
    }

    /**
     * Retrieve access token
     */
    private function retrieveAccessToken(): ?Token
    {
        $tokenString = $this->cacheManager->getValue(self::ACCESS_TOKEN_KEY);

        if ($tokenString === null) {
            return null;
        }

        return (new Token\Parser(new JoseEncoder))->parse($tokenString);
    }

    /**
     * Retrieve refresh token
     */
    private function retrieveRefreshToken(): ?Token
    {
        $tokenString = $this->cacheManager->getValue(self::REFRESH_TOKEN_KEY);

        if ($tokenString === null) {
            return null;
        }

        return (new Token\Parser(new JoseEncoder))->parse($tokenString);
    }

    /**
     * Clear all tokens
     */
    public function clearTokens(): bool
    {
        $accessDeleted = $this->cacheManager->delete(self::ACCESS_TOKEN_KEY);
        $refreshDeleted = $this->cacheManager->delete(self::REFRESH_TOKEN_KEY);

        return $accessDeleted && $refreshDeleted;
    }
}
