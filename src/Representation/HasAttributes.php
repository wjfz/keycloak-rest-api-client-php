<?php

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;

trait HasAttributes
{
    /**
     * @return Map|array<string,mixed>|null
     */
    public function getAttributes(): Map|array|null
    {
        if (! $this->attributes) {
            return null;
        }

        return is_array($this->attributes) ? new Map($this->attributes) : $this->attributes;
    }

    /**
     * @param  Map|array<string,mixed>  $attributes
     */
    public function withAttributes(Map|array $attributes): static
    {
        $new = clone $this;
        $new->attributes = is_array($attributes) ? new Map($attributes) : $attributes;

        return $new;
    }
}
