<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Role;

/**
 * @extends Collection<Role>
 *
 * @codeCoverageIgnore
 */
class RoleCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Role::class;
    }
}
