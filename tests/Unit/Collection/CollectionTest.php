<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Collection;

use InvalidArgumentException;
use Overtrue\Keycloak\Collection\Collection;
use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Collection\UserCollection;
use Overtrue\Keycloak\Representation\Realm;
use Overtrue\Keycloak\Representation\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Collection::class)]
class CollectionTest extends TestCase
{
    public function test_can_create_collection_with_expected_representations(): void
    {
        $collection = new UserCollection([
            new User,
            new User,
            new User,
        ]);

        static::assertCount(3, $collection);
    }

    public function test_throws_exception_if_unexpected_representation_is_provided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UserCollection expects items to be User representation, Realm given');

        // @phpstan-ignore-next-line
        new UserCollection([new Realm]);
    }

    public function test_throws_exception_if_unexpected_representation_should_be_added(): void
    {
        $collection = new UserCollection([
            new User,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UserCollection expects items to be User representation, Realm given');

        $collection->add(new Realm);
    }

    public function test_can_get_iterator(): void
    {
        $collection = new RealmCollection([
            new Realm,
            new Realm,
            new Realm,
        ]);

        foreach ($collection as $realm) {
            static::assertInstanceOf(Realm::class, $realm);
        }
    }

    public function test_serialize_empty_collection(): void
    {
        $collection = new RealmCollection([]);

        static::assertEquals([], $collection->jsonSerialize());
    }

    public function test_first_returns_first_item_in_collection(): void
    {
        $collection = new RealmCollection([
            new Realm(realm: 'first'),
            new Realm(realm: 'second'),
            new Realm(realm: 'third'),
        ]);

        $realm = $collection->first();

        static::assertInstanceOf(Realm::class, $realm);
        static::assertSame('first', $realm->getRealm());
    }

    public function test_first_returns_null_if_collection_is_empty(): void
    {
        $collection = new RealmCollection([]);

        static::assertNull($collection->first());
    }
}
