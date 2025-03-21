<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Type;

use OutOfBoundsException;
use Overtrue\Keycloak\Type\AnyMap;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\BooleanMap;
use Overtrue\Keycloak\Type\IntegerMap;
use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Map::class)]
class MapTest extends TestCase
{
    public function test_can_be_constructed_from_empty_array(): void
    {
        $maps = [new StringMap(), new ArrayMap(), new BooleanMap(), new IntegerMap(), new AnyMap()];

        foreach ($maps as $map) {
            self::assertEquals(
                [],
                $map->jsonSerialize(),
            );
        }
    }

    public static function maps()
    {
        return [
            'array map' => [
                ['key-1' => ['value-1'], 'key-2' => ['value-2'], 'key-3' => ['value-3']],
                new ArrayMap([
                    'key-1' => ['value-1'],
                    'key-2' => 'value-2',
                    'key-3' => ['value-3'],
                ])
            ],

            'boolean map' => [
                ['key-1' => true, 'key-2' => false, 'key-3' => true, 'key-4' => false],
                new BooleanMap([
                    'key-1' => true,
                    'key-2' => false,
                    'key-3' => 1,
                    'key-4' => 0,
                ])
            ],

            'integer map' => [
                ['key-1' => 1, 'key-2' => 2, 'key-3' => 3],
                new IntegerMap([
                    'key-1' => 1,
                    'key-2' => 2,
                    'key-3' => '3a',
                ])
            ],

            'string map' => [
                ['key-1' => 'value-1', 'key-2' => 'value-2', 'key-3' => 'value-3'],
                new StringMap([
                    'key-1' => 'value-1',
                    'key-2' => 'value-2',
                    'key-3' => 'value-3',
                ])
            ],

            'any map' => [
                ['key-1' => 'value-1', 'key-2' => ['value-2'], 'key-3' => 'value-3'],
                new AnyMap([
                    'key-1' => 'value-1',
                    'key-2' => ['value-2'],
                    'key-3' => 'value-3',
                ])
            ],
        ];
    }

    /**
     * @dataProvider maps
     */
    public function test_be_constructed_from_filled_array($expected, $map): void
    {
        self::assertEquals($expected, $map->jsonSerialize());
    }

    public function test_can_be_iterated(): void
    {
        $map = new ArrayMap([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => 'value-3',
        ]);

        foreach ($map as $key => $value) {
            static::assertStringStartsWith('key-', $key);
            static::assertIsArray($value);
            static::assertCount(1, $value);
            static::assertStringStartsWith('value-', $value[0]);
        }
    }

    public function test_can_be_counted(): void
    {
        $map = new StringMap([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => 'value-3',
        ]);

        static::assertCount(3, $map);
    }

    public function test_contains(): void
    {
        $map = new StringMap(['key-1' => 'value-1', 'key-2' => 'value-2']);

        static::assertTrue($map->contains('key-1'));
        static::assertTrue($map->contains('key-2'));
        static::assertFalse($map->contains('key-3'));
    }

    public function test_contains_value()
    {
        $map = new AnyMap([
            'key-1' => 'value-1',
            'key-2' => 'value-2',
            'key-3' => ['value-31', 'value-32'],
            'key-4' => false,
            'key-5' => true,
            'key-6' => 0,
            'key-7' => 1,
        ]);

        static::assertTrue($map->containsValue('value-1'));
        static::assertTrue($map->containsValue('value-2'));
        static::assertFalse($map->containsValue('value-3'));
        static::assertFalse($map->containsValue('value-31'));
        static::assertFalse($map->containsValue('value-32'));
        static::assertTrue($map->containsValue(['value-31', 'value-32']));
        static::assertTrue($map->containsValue(false));
    }

    public function test_get(): void
    {
        $map = new StringMap(['key-1' => 'value-1', 'key-2' => 'value-2']);

        static::assertSame('value-1', $map->get('key-1'));
        static::assertSame('value-2', $map->get('key-2'));

        static::assertSame('default-value', $map->get('key-22222', 'default-value'));
    }

    public function test_get_first()
    {
        $map = new AnyMap(['key-1' => 'value-1', 'key-2' => ['value-3', 'value-4']]);

        static::assertSame('value-1', $map->getFirst('key-1'));
        static::assertSame('value-3', $map->getFirst('key-2'));
        static::assertSame('default-value', $map->getFirst('key-6666', 'default-value'));
    }

    public function test_contains_key()
    {
        $map = new StringMap(['key-1' => 'value-1', 'key-2' => 'value-2']);

        static::assertTrue($map->containsKey('key-1'));
        static::assertTrue($map->containsKey('key-2'));
        static::assertFalse($map->containsKey('key-3'));
    }

    public function test_with(): void
    {
        $map = new StringMap(['key-1' => 'value-1', 'key-2' => 'value-2']);

        $updatedMap = $map->with('key-3', 'value-3');

        static::assertNotSame($map, $updatedMap);
        static::assertCount(2, $map);
        static::assertCount(3, $updatedMap);
    }

    public function test_without(): void
    {
        $map = new StringMap(['key-1' => 'value-1', 'key-2' => 'value-2']);

        $updatedMap = $map->without('key-2');

        static::assertNotSame($map, $updatedMap);
        static::assertCount(2, $map);
        static::assertCount(1, $updatedMap);
    }

    public function test_get_map(): void
    {
        $inner = ['key-1' => 'value-1', 'key-2' => 'value-2'];
        $map = new StringMap($inner);

        static::assertSame($inner, $map->getMap());
    }
}
