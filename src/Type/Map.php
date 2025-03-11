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
 * @implements IteratorAggregate<string, T>
 * @phpstan-consistent-constructor
 */
abstract class Map extends Type implements Countable, IteratorAggregate
{
    /**
     * @var array<string, T>
     */
    protected array $map = [];

    /**
     * @param  array<string, T>  $map
     */
    public function __construct(array $map = [])
    {
        $this->map = array_map($this->normalizeValue(...), $map);
    }

    /**
     * @param \Overtrue\Keycloak\Type\Map|array<string, mixed>|null $map
     *
     * @return static|null
     */
    public static function make(Map|array|null $map): ?static
    {
        if (! $map) {
            return null;
        }

        // @phpstan-ignore return.type
        return is_array($map) ? new static($map) : $map;
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
        return in_array($value, $this->map, true);
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
     * @param  T  $value
     * @return $this
     */
    public function with(string $key, mixed $value): self
    {
        $clone = clone $this;

        $clone->map[$key] = $this->normalizeValue($value);

        return $clone;
    }

    protected function normalizeValue(mixed $value): mixed
    {
        return $value;
    }

    public function without(string $key): self
    {
        $clone = clone $this;

        unset($clone->map[$key]);

        return $clone;
    }

    /**
     * @return array<string, T>
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
