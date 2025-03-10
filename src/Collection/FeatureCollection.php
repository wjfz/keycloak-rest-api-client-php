<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Feature;

/**
 * @extends Collection<Feature>
 *
 * @codeCoverageIgnore
 */
class FeatureCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return Feature::class;
    }
}
