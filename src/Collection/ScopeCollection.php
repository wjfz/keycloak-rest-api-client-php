<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Scope;

/**
 * @extends Collection<Scope>
 *
 * @codeCoverageIgnore
 */
class ScopeCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Scope::class;
    }
}
