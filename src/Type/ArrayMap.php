<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

/**
 * @template T
 */
class ArrayMap extends Map
{
    #[\Override]
    protected function normalizeValue(mixed $value): array
    {
        return (array) $value;
    }
}
