<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Serializer;

use ArrayObject;
use Generator;
use InvalidArgumentException;
use Overtrue\Keycloak\Serializer\MapNormalizer;
use Overtrue\Keycloak\Type\AnyMap;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\BooleanMap;
use Overtrue\Keycloak\Type\IntegerMap;
use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MapNormalizer::class)]
class MapNormalizerTest extends TestCase
{
    public function test_supported_types(): void
    {
        $normalizer = new MapNormalizer;

        static::assertSame(
            [Map::class => true],
            $normalizer->getSupportedTypes('json'),
        );
    }

    public function test_supports_normalization(): void
    {
        $normalizer = new MapNormalizer;

        static::assertTrue($normalizer->supportsNormalization(new StringMap()));
        static::assertFalse($normalizer->supportsNormalization([]));
    }

    #[DataProvider('maps')]
    public function test_normalize(mixed $value, ArrayObject $expected): void
    {
        $normalizer = new MapNormalizer;

        static::assertEquals(
            $expected,
            $normalizer->normalize($value, get_class($value)),
        );
    }

    public function test_throws_if_data_is_not_a_map(): void
    {
        $normalizer = new MapNormalizer;

        static::expectException(InvalidArgumentException::class);
        static::expectExceptionMessage(sprintf('Data must be an instance of "%s"', Map::class));

        $normalizer->normalize([]);
    }

    public static function maps(): Generator
    {
        yield 'filled integer map' => [
            new IntegerMap([
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ]),
            new ArrayObject([
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ]),
        ];

        yield 'filled array map' => [
            new ArrayMap([
                'a' => [1],
                'b' => 2,
                'c' => [3],
            ]),
            new ArrayObject([
                'a' => [1],
                'b' => [2],
                'c' => [3],
            ]),
        ];

        yield 'filled boolean map' => [
            new BooleanMap([
                'view' => true,
                'edit' => 1,
            ]),
            new ArrayObject([
                'view' => true,
                'edit' => true,
            ]),
        ];

        yield 'filled string map' => [
            new StringMap([
                'creator_id' => 'user-id',
                'avatar' => 'https://example.com/avatar.png',
            ]),
            new ArrayObject([
                'creator_id' => 'user-id',
                'avatar' => 'https://example.com/avatar.png',
            ]),
        ];

        yield 'filled any map' => [
            new AnyMap([
                'a' => 1,
                'b' => '2',
                'c' => [3],
                'creator_id' => 'user-id',
                'avatar' => 'https://example.com/avatar.png',
            ]),
            new ArrayObject([
                'a' => 1,
                'b' => '2',
                'c' => [3],
                'creator_id' => 'user-id',
                'avatar' => 'https://example.com/avatar.png',
            ]),
        ];

        yield 'empty map' => [
            new IntegerMap(),
            new ArrayObject,
        ];
    }
}
