<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\User;

/**
 * @extends Collection<User>
 *
 * @codeCoverageIgnore
 */
class UserCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return User::class;
    }
}
