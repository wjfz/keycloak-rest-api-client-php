<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Serializer;

use Generator;
use Overtrue\Keycloak\Serializer\MapDenormalizer;
use Overtrue\Keycloak\Type\AnyMap;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\BooleanMap;
use Overtrue\Keycloak\Type\IntegerMap;
use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MapDenormalizer::class)]
class MapDenormalizerTest extends TestCase
{
    public function test_supported_types(): void
    {
        $denormalizer = new MapDenormalizer;

        static::assertSame(
            [Map::class => true],
            $denormalizer->getSupportedTypes('json'),
        );
    }

    public function test_supports_denormalization(): void
    {
        $denormalizer = new MapDenormalizer;

        static::assertTrue($denormalizer->supportsDenormalization([], Map::class));
        static::assertFalse($denormalizer->supportsDenormalization([], 'array'));
    }

    #[DataProvider('maps')]
    public function test_denormalize(mixed $value, Map $expected): void
    {
        $denormalizer = new MapDenormalizer;

        self::assertEquals(
            $expected,
            $denormalizer->denormalize($value, get_class($expected)),
        );
    }

    public static function maps(): Generator
    {
        yield 'filled integer map' => [
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            new IntegerMap([
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ]),
        ];

        yield 'filled string map' => [
            [
                'a' => 'a1',
                'b' => 'a2',
                'c' => 'a3',
            ],
            new StringMap([
                'a' => 'a1',
                'b' => 'a2',
                'c' => 'a3',
            ]),
        ];

        yield 'filled array map' => [
            [
                'a' => ['a1'],
                'b' => ['a2'],
                'c' => ['a3'],
            ],
            new ArrayMap([
                'a' => ['a1'],
                'b' => ['a2'],
                'c' => ['a3'],
            ]),
        ];

        yield 'filled boolean map' => [
            [
                'a' => true,
                'b' => false,
                'c' => false,
            ],
            new BooleanMap([
                'a' => true,
                'b' => false,
                'c' => false,
            ]),
        ];

        yield 'empty array' => [
            [],
            new AnyMap,
        ];

        yield 'non-array' => [
            1337,
            new AnyMap,
        ];
    }
}
