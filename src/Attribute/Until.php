<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Attribute;

use Attribute;

/**
 * @internal
 */
#[Attribute]
class Until
{
    public function __construct(
        public readonly string $version,
    ) {}
}
