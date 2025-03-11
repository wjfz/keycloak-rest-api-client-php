<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

class IntegerMap extends Map
{
    #[\Override]
    protected function normalizeValue(mixed $value): int
    {
        return (int) $value;
    }
}
