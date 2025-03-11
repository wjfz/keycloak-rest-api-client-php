<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Type;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use ReturnTypeWillChange;
use Traversable;

/**
 * @template T
 *
 * @implements IteratorAggregate<string, T[]>
 */
class Map extends Type implements Countable, IteratorAggregate
{
    /**
     * @var array<string, T[]>
     */
    private array $map = [];

    /**
     * @param  array<string, T[]|T>  $map
     */
    public function __construct(array $map = [])
    {
        foreach ($map as $key => $value) {
            $this->map[$key] = (array) $value;
        }
    }

    #[ReturnTypeWillChange]
    #[\Override]
    public function jsonSerialize()
    {
        return $this->map;
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->map);
    }

    #[\Override]
    public function count(): int
    {
        return count($this->map);
    }

    public function contains(string $key): bool
    {
        return array_key_exists($key, $this->map);
    }

    public function containsKey(string $key): bool
    {
        return array_key_exists($key, $this->map);
    }

    public function containsValue(mixed $value): bool
    {
        return array_any($this->map, function ($v) use ($value) {
            if (! is_array($value)) {
                return in_array($value, $v, true);
            }

            return $v == $value;
        });
    }

    public function get(string $key): mixed
    {
        if (! $this->contains($key)) {
            throw new OutOfBoundsException(sprintf('Key "%s" does not exist in map', $key));
        }

        return $this->map[$key];
    }

    /**
     * @return T|mixed
     */
    public function getFirst(string $key, mixed $default = null): mixed
    {
        return $this->get($key)[0] ?? $default;
    }

    /**
     * @param  T|T[]  $value
     * @return $this
     */
    public function with(string $key, mixed $value): self
    {
        $clone = clone $this;

        $clone->map[$key] = (array) $value;

        return $clone;
    }

    public function without(string $key): self
    {
        $clone = clone $this;

        unset($clone->map[$key]);

        return $clone;
    }

    /**
     * @return array<string, T[]>
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @return array<string>
     */
    public function nameSet(): array
    {
        return array_keys($this->map);
    }
}
