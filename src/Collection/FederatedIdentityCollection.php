<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\FederatedIdentity;

/**
 * @extends Collection<FederatedIdentity>
 *
 * @codeCoverageIgnore
 */
class FederatedIdentityCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return FederatedIdentity::class;
    }
}
