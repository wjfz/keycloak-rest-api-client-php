<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

/**
 * @template T extends integer
 *
 * @template-extends Map<T>
 */
class IntegerMap extends Map
{
    /**
     * @param  int  $value
     */
    #[\Override]
    protected function normalizeValue(mixed $value): int
    {
        return (int) $value;
    }
}
