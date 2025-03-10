<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\PolicyCollection;
use Overtrue\Keycloak\Collection\ResourceCollection;

/**
 * @codeCoverageIgnore
 */
class Scope extends Representation
{
    public function __construct(
        protected ?string $displayName = null,
        protected ?string $iconUri = null,
        protected ?string $id = null,
        protected ?string $name = null,
        protected ?PolicyCollection $policies = null,
        protected ?ResourceCollection $resources = null,
    ) {}
}
