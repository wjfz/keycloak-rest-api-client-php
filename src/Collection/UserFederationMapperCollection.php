<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\UserFederationMapper;

/**
 * @extends Collection<UserFederationMapper>
 *
 * @codeCoverageIgnore
 */
class UserFederationMapperCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return UserFederationMapper::class;
    }
}
