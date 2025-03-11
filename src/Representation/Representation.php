<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use BadMethodCallException;
use JsonSerializable;
use Overtrue\Keycloak\Exception\PropertyDoesNotExistException;
use Overtrue\Keycloak\Json\JsonDecoder;
use ReflectionClass;
use ReflectionProperty;

abstract class Representation implements JsonSerializable
{
    abstract public function __construct();

    /**
     * @param  array<string, mixed>  $properties
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public static function from(array $properties): static
    {
        $representation = new static;

        foreach ($properties as $property => $value) {
            $representation = $representation->withProperty($property, $value);
        }

        return $representation;
    }

    public static function fromJson(string $json): static
    {
        return static::from(
            (new JsonDecoder)->decode($json),
        );
    }

    /**
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    public function with(string $property, mixed $value): static
    {
        return $this->withProperty($property, $value);
    }

    /**
     * @return array<string, mixed>
     */
    final public function jsonSerialize(): array
    {
        $serializable = [];
        $reflectedClass = (new ReflectionClass($this));
        $properties = $reflectedClass->getProperties(ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $serializable[$property->getName()] = ($property->getValue($this) instanceof JsonSerializable)
                ? $property->getValue($this)->jsonSerialize()
                : $property->getValue($this);
        }

        return $serializable;
    }

    /**
     * @return array<string, mixed>
     */
    final public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * @param  string[]  $arguments
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    final public function __call(string $name, array $arguments): mixed
    {
        if (str_starts_with($name, 'get')) {
            return $this->__get(lcfirst(substr($name, 3)));
        }

        if (str_starts_with($name, 'with')) {
            return $this->with(lcfirst(substr($name, 4)), $arguments[0]);
        }

        throw new BadMethodCallException;
    }

    /**
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    final public function __get(string $name): mixed
    {
        return $this->getProperty($name);
    }

    /**
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    private function getProperty(string $name): mixed
    {
        $getter = 'get'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        $this->throwExceptionIfPropertyDoesNotExist($name);

        return $this->$name;
    }

    /**
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     */
    private function withProperty(string $property, mixed $value): static
    {
        $clone = clone $this;

        $setter = 'set'.ucfirst($property);
        if (method_exists($this, $setter)) {
            $clone->$setter($value);

            return $clone;
        }

        $this->throwExceptionIfPropertyDoesNotExist($property);

        $clone->$property = $value;

        return $clone;
    }

    /**
     * @throws PropertyDoesNotExistException
     */
    private function throwExceptionIfPropertyDoesNotExist(string $property): void
    {
        if (! property_exists(static::class, $property)) {
            throw new PropertyDoesNotExistException(
                sprintf(
                    'Property "%s" does not exist in "%s"',
                    $property,
                    static::class,
                ),
            );
        }
    }
}
