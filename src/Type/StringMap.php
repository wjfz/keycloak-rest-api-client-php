<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

/**
 * @template T extends string
 *
 * @template-extends Map<T>
 */
class StringMap extends Map
{
    /**
     * @param  string  $value
     */
    #[\Override]
    protected function normalizeValue(mixed $value): string
    {
        return (string) $value;
    }
}
