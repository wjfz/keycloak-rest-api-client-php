<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\AuthenticatorConfig;

/**
 * @extends Collection<AuthenticatorConfig>
 *
 * @codeCoverageIgnore
 */
class AuthenticatorConfigCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return AuthenticatorConfig::class;
    }
}
