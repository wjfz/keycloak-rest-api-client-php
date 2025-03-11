<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Organization;

/**
 * @extends Collection<Organization>
 *
 * @codeCoverageIgnore
 */
class OrganizationCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Organization::class;
    }
}
