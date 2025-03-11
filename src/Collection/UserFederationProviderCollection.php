<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\UserFederationProvider;

/**
 * @extends Collection<UserFederationProvider>
 *
 * @codeCoverageIgnore
 */
class UserFederationProviderCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return UserFederationProvider::class;
    }
}
