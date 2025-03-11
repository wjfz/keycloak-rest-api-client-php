<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\OAuth\TokenStorage;

use Lcobucci\JWT\Token;
use Overtrue\Keycloak\OAuth\TokenStorageInterface;

/**
 * @internal
 */
class InMemory implements TokenStorageInterface
{
    private ?Token $accessToken = null;

    private ?Token $refreshToken = null;

    #[\Override]
    public function storeAccessToken(Token $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    #[\Override]
    public function storeRefreshToken(Token $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    #[\Override]
    public function retrieveAccessToken(): ?Token
    {
        return $this->accessToken;
    }

    #[\Override]
    public function retrieveRefreshToken(): ?Token
    {
        return $this->refreshToken;
    }
}
