<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Group;

/**
 * @extends Collection<Group>
 *
 * @codeCoverageIgnore
 */
class GroupCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return Group::class;
    }
}
