<?php

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;

interface AttributesAwareInterface
{
    public function getAttributes(): array|Map|null;

    public function withAttributes(array|Map $attributes): static;
}
