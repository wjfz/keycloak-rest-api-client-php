<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\IdentityProviderMapper;

/**
 * @extends Collection<IdentityProviderMapper>
 *
 * @codeCoverageIgnore
 */
class IdentityProviderMapperCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return IdentityProviderMapper::class;
    }
}
