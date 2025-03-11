<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ScopeMapping;

/**
 * @extends Collection<ScopeMapping>
 *
 * @codeCoverageIgnore
 */
class ScopeMappingCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return ScopeMapping::class;
    }
}
