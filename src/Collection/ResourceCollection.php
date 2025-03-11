<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Resource;

/**
 * @extends Collection<Resource>
 *
 * @codeCoverageIgnore
 */
class ResourceCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Resource::class;
    }
}
