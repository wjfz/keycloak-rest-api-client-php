<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\OrganizationDomain;

/**
 * @extends Collection<OrganizationDomain>
 *
 * @codeCoverageIgnore
 */
class OrganizationDomainCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return OrganizationDomain::class;
    }
}
