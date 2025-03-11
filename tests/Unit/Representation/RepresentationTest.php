<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Representation;

use BadMethodCallException;
use Overtrue\Keycloak\Exception\PropertyDoesNotExistException;
use Overtrue\Keycloak\Test\Unit\Stub\Representation;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Overtrue\Keycloak\Representation\Representation::class)]
class RepresentationTest extends TestCase
{
    public function test_throws_exception_when_trying_to_modify_property_which_does_not_exist(): void
    {
        $representation = new Representation;

        $this->expectException(PropertyDoesNotExistException::class);
        $representation->with('doesNotExist', 'value');
    }

    public function test_throws_exception_when_trying_to_construct_from_json_and_property_does_not_exist(): void
    {
        $this->expectException(PropertyDoesNotExistException::class);
        Representation::fromJson(json_encode([
            'doesNotExist' => 'value',
        ], JSON_THROW_ON_ERROR));
    }

    public function test_throws_exception_when_trying_to_construct_from_properties_and_property_does_not_exist(): void
    {
        $this->expectException(PropertyDoesNotExistException::class);
        Representation::from([
            'doesNotExist' => 'value',
        ]);
    }

    public function test_existing_property_can_be_modified(): void
    {
        $representation = new Representation;
        $modifiedRepresentation = $representation->withMap(
            new StringMap([
                'key' => 'value',
            ]),
        );

        static::assertNull($representation->getMap());
        static::assertEquals(
            new StringMap([
                'key' => 'value',
            ]),
            $modifiedRepresentation->getMap(),
        );
        static::assertNotSame($representation, $modifiedRepresentation);
    }

    public function test_can_be_constructed_from_json(): void
    {
        $representation = Representation::fromJson(json_encode([
            'since2000' => 'since2000-value',
            'until1400' => 'until1400-value',
            'since1500Until1800' => 'since1500Until1800-value',
        ], JSON_THROW_ON_ERROR));

        static::assertSame($representation->getSince2000(), 'since2000-value');
        static::assertSame($representation->getUntil1400(), 'until1400-value');
        static::assertSame($representation->getSince1500Until1800(), 'since1500Until1800-value');
    }

    public function test_can_be_constructed_from_properties_array(): void
    {
        $representation = Representation::from([
            'since2000' => 'since2000-value',
            'until1400' => 'until1400-value',
            'since1500Until1800' => 'since1500Until1800-value',
        ]);

        static::assertSame($representation->getSince2000(), 'since2000-value');
        static::assertSame($representation->getUntil1400(), 'until1400-value');
        static::assertSame($representation->getSince1500Until1800(), 'since1500Until1800-value');
    }

    public function test_json_serializes_scalar_types_correctly(): void
    {
        $representation = Representation::from([
            'since2000' => 'since2000-value',
            'until1400' => 'until1400-value',
            'since1500Until1800' => 'since1500Until1800-value',
        ]);

        static::assertSame(
            [
                'since2000' => 'since2000-value',
                'until1400' => 'until1400-value',
                'since1500Until1800' => 'since1500Until1800-value',
                'map' => null,
            ],
            $representation->jsonSerialize(),
        );
    }

    public function test_json_serializes_map_correctly(): void
    {
        $map = new ArrayMap([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => 'value-3',
        ]);

        $representation = Representation::from([
            'since2000' => 'since2000-value',
            'until1400' => 'until1400-value',
            'since1500Until1800' => 'since1500Until1800-value',
            'map' => $map,
        ]);

        $jsonSerialized = $representation->jsonSerialize();

        static::assertSame([
            'key-1' => ['value-1'],
            'key-2' => ['value-2'],
            'key-3' => ['value-3'],
        ], $jsonSerialized['map']);
    }

    public function test_serializes_map_correctly_when_only_array_is_provided(): void
    {
        $array = [
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => 'value-3',
        ];

        $map = new StringMap($array);

        $representation = new Representation;
        $representation = $representation->with('map', new StringMap([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => 'value-3',
        ]));

        static::assertInstanceOf(Map::class, $representation->getMap());
        static::assertEquals($map, $representation->getMap());
    }

    public function test_throws_if_property_does_not_exist(): void
    {
        $representation = new Representation;

        $this->expectException(BadMethodCallException::class);

        // @phpstan-ignore-next-line
        $representation->doesNotExist();
    }
}
