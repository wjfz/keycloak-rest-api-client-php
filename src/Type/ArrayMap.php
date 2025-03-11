<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

/**
 * @template T extends array<mixed>
 * @template-extends Map<T>
 */
class ArrayMap extends Map
{
    /**
     * @param array<T>|T $value
     *
     * @return array<T>
     */
    #[\Override]
    protected function normalizeValue(mixed $value): array
    {
        return (array) $value;
    }
}
