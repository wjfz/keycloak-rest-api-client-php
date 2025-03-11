<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\AuthenticationFlow;

/**
 * @extends Collection<AuthenticationFlow>
 *
 * @codeCoverageIgnore
 */
class AuthenticationFlowCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return AuthenticationFlow::class;
    }
}
