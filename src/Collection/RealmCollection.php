<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Realm;

/**
 * @extends Collection<Realm>
 *
 * @codeCoverageIgnore
 */
class RealmCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return Realm::class;
    }
}
