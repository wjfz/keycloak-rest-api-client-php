<?php

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;

interface AttributesAwareInterface
{
    /**
     * @return Map|array<string, mixed>|null
     */
    public function getAttributes(): array|Map|null;

    /**
     * @param  Map|array<string, mixed>  $attributes
     */
    public function withAttributes(array|Map $attributes): static;
}
