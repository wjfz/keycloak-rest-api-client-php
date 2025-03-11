<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

class StringMap extends Map
{
    #[\Override]
    protected function normalizeValue(mixed $value): string
    {
        return (string) $value;
    }
}
