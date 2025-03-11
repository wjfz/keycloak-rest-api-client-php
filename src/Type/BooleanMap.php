<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

class BooleanMap extends Map
{
    #[\Override]
    protected function normalizeValue(mixed $value): bool
    {
        return (bool) $value;
    }
}
