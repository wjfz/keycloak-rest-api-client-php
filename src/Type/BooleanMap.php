<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

/**
 * @template T extends boolean
 *
 * @template-extends Map<T>
 */
class BooleanMap extends Map
{
    /**
     * @param  bool  $value
     */
    #[\Override]
    protected function normalizeValue(mixed $value): bool
    {
        return (bool) $value;
    }
}
