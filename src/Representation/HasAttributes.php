<?php

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;

trait HasAttributes
{
    public function getAttributes(): Map|array|null
    {
        return $this->attributes instanceof Map ? $this->attributes : $this->normalizeAttributes($this->attributes);
    }

    public function withAttributes(Map|array $attributes): static
    {
        $new = clone $this;
        $new->attributes = new Map($this->normalizeAttributes($attributes));

        return $new;
    }

    public function normalizeAttributes(Map|array $attributes): array|Map
    {
        foreach ($attributes as $attribute => $value) {
            // Ensure that the value is an array.
            $attributes[$attribute] = (array) $value;
        }

        return $attributes;
    }
}
