<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Stub;

/**
 * @extends \Overtrue\Keycloak\Collection\Collection<Representation>
 */
class Collection extends \Overtrue\Keycloak\Collection\Collection
{
    public static function getRepresentationClass(): string
    {
        return Representation::class;
    }
}
