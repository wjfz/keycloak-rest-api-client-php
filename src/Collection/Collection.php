<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Overtrue\Keycloak\Representation\Representation;
use ReflectionClass;
use Traversable;

/**
 * @template T extends Representation
 *
 * @implements IteratorAggregate<T>
 */
abstract class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @var array<array-key, T>
     */
    protected array $items = [];

    /**
     * @param  iterable<T|array<string, mixed>>  $items
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     * @throws \ReflectionException
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @return class-string<T>
     */
    abstract public static function getRepresentationClass(): string;

    #[\Override]
    public function count(): int
    {
        return count($this->items);
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return array<array-key, T>
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * @param  \Overtrue\Keycloak\Representation\Representation|array<string, mixed>  $item
     *
     * @throws \ReflectionException
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function add(Representation|array $item): void
    {
        /** @var class-string<T> $expectedRepresentationClass */
        $expectedRepresentationClass = $this->getRepresentationClass();

        if (! $item instanceof $expectedRepresentationClass && ! is_array($item)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s expects items to be %s representation, %s given',
                    new ReflectionClass(static::class)->getShortName(),
                    new ReflectionClass($expectedRepresentationClass)->getShortName(),
                    new ReflectionClass($item)->getShortName(),
                ),
            );
        }

        $this->items[] = $item instanceof $expectedRepresentationClass ? $item : $expectedRepresentationClass::from($item);
    }

    public function first(): ?Representation
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return array<array-key, T>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return array<array-key, T>
     */
    public function all(): array
    {
        return $this->toArray();
    }
}
