<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Serializer;

use Overtrue\Keycloak\Collection\ClientCollection;
use Overtrue\Keycloak\Collection\Collection;
use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Representation\Group;
use Overtrue\Keycloak\Serializer\CollectionDenormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

#[CoversClass(CollectionDenormalizer::class)]
class CollectionDenormalizerTest extends TestCase
{
    public function test_supported_types(): void
    {
        $denormalizer = new CollectionDenormalizer(new PropertyNormalizer);

        static::assertSame(
            [Collection::class => true],
            $denormalizer->getSupportedTypes('json'),
        );
    }

    public function test_supports_denormalization(): void
    {
        $denormalizer = new CollectionDenormalizer(new PropertyNormalizer);

        static::assertTrue($denormalizer->supportsDenormalization([], ClientCollection::class));
        static::assertFalse($denormalizer->supportsDenormalization([], 'array'));
    }

    public function test_serializes_collection(): void
    {
        $denormalizer = new CollectionDenormalizer(new PropertyNormalizer);

        $groupCollection = $denormalizer->denormalize(
            [new Group, new Group],
            GroupCollection::class,
        );

        static::assertInstanceOf(GroupCollection::class, $groupCollection);
        static::assertCount(2, $groupCollection);
    }
}
