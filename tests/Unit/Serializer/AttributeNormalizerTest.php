<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Serializer;

use Generator;
use Overtrue\Keycloak\Representation\Representation;
use Overtrue\Keycloak\Serializer\AttributeNormalizer;
use Overtrue\Keycloak\Test\Unit\Stub\Representation as StubRepresentation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

#[CoversClass(AttributeNormalizer::class)]
class AttributeNormalizerTest extends TestCase
{
    public function test_supports_representation(): void
    {
        $normalizer = new AttributeNormalizer(new PropertyNormalizer);

        static::assertSame(
            [Representation::class => true],
            $normalizer->getSupportedTypes(null),
        );

        static::assertTrue($normalizer->supportsNormalization(new StubRepresentation));

        static::assertFalse($normalizer->supportsNormalization(new stdClass));
    }

    public function test_does_not_filter_properties_if_version_is_not_provided(): void
    {
        $representation = new StubRepresentation;
        $normalizer = new AttributeNormalizer(new PropertyNormalizer, null);

        $filteredProperties = $normalizer->normalize($representation);

        static::assertIsArray($filteredProperties);
        static::assertArrayHasKey('since2000', $filteredProperties);
        static::assertArrayHasKey('until1400', $filteredProperties);
        static::assertArrayHasKey('since1500Until1800', $filteredProperties);
    }

    #[DataProvider('supportedKeycloakVersions')]
    public function test_filters_out_property_which_has_not_yet_been_introduced(string $version): void
    {
        $representation = new StubRepresentation;
        $normalizer = new AttributeNormalizer(new PropertyNormalizer, $version);

        $filteredProperties = $normalizer->normalize($representation);
        static::assertIsArray($filteredProperties);

        if ((int) $version < 20) {
            static::assertArrayNotHasKey('since2000', $filteredProperties);
        } else {
            static::assertArrayHasKey('since2000', $filteredProperties);
        }
    }

    #[DataProvider('supportedKeycloakVersions')]
    public function test_filters_out_property_which_has_been_removed(string $version): void
    {
        $representation = new StubRepresentation;
        $normalizer = new AttributeNormalizer(new PropertyNormalizer, $version);

        $filteredProperties = $normalizer->normalize($representation);
        static::assertIsArray($filteredProperties);

        if ((int) $version > 14) {
            static::assertArrayNotHasKey('until1400', $filteredProperties);
        } else {
            static::assertArrayHasKey('until1400', $filteredProperties);
        }
    }

    #[DataProvider('supportedKeycloakVersions')]
    public function test_filters_out_property_which_has_been_introduced_and_removed(string $version): void
    {
        $representation = new StubRepresentation;
        $normalizer = new AttributeNormalizer(new PropertyNormalizer, $version);

        $filteredProperties = $normalizer->normalize($representation);
        static::assertIsArray($filteredProperties);

        if ((int) $version < 15 || (int) $version > 18) {
            static::assertArrayNotHasKey('since1500Until1800', $filteredProperties);
        } else {
            static::assertArrayHasKey('since1500Until1800', $filteredProperties);
        }
    }

    public function test_memoizes_filtered_properties_of_representation(): void
    {
        $representation = new StubRepresentation;
        $normalizer = new AttributeNormalizer(new PropertyNormalizer, '20.0.0');

        $reflection = new ReflectionClass($normalizer);
        $reflection->getProperty('filteredProperties')->setAccessible(true);

        $memoizedFilteredProperties = $reflection->getProperty('filteredProperties')->getValue($normalizer);
        static::assertArrayNotHasKey($representation::class, $memoizedFilteredProperties);

        $normalizer->normalize($representation);
        $normalizer->normalize($representation);

        $memoizedFilteredProperties = $reflection->getProperty('filteredProperties')->getValue($normalizer);
        static::assertArrayHasKey($representation::class, $memoizedFilteredProperties);
    }

    public static function supportedKeycloakVersions(): Generator
    {
        yield ['24.0.0'];
        yield ['25.0.0'];
        yield ['26.0.0'];
    }
}
