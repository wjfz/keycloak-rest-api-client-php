<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\IdentityProvider;

/**
 * @extends Collection<IdentityProvider>
 *
 * @codeCoverageIgnore
 */
class IdentityProviderCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return IdentityProvider::class;
    }
}
