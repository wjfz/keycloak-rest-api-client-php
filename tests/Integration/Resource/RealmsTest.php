<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Integration\Resource;

use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Representation\Realm;
use Overtrue\Keycloak\Test\Integration\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class RealmsTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function test_can_get_all_realms(): void
    {
        $realms = $this->getKeycloak()->realms()->all();

        static::assertInstanceOf(RealmCollection::class, $realms);
        static::assertGreaterThanOrEqual(1, $realms->count());
    }

    public function test_can_get_realm(): void
    {
        $realm = $this->getKeycloak()->realms()->get(realm: 'master');

        static::assertEquals('master', $realm->getRealm());
    }

    public function test_can_get_realm_keys(): void
    {
        $realmkeys = $this->getKeycloak()->realms()->keys(realm: 'master');
        static::assertArrayHasKey('AES', $realmkeys->getActive());
        static::assertGreaterThan(1, $realmkeys->getKeys()->count());
    }

    public function test_can_update_realm(): void
    {
        $realm = $this->getKeycloak()->realms()->get(realm: 'master');

        static::assertFalse($realm->getRegistrationAllowed());

        $realm = $realm->withRegistrationAllowed(true);
        $realm = $this->keycloak->realms()->update($realm->getRealm(), $realm);

        static::assertTrue($realm->getRegistrationAllowed());
    }

    public function test_can_import_realm(): void
    {
        $realm = new Realm(id: 'testing-id', realm: 'testing-realm');
        $realm = $this->getKeycloak()->realms()->import(realm: $realm);

        static::assertEquals('testing-id', $realm->getId());
        static::assertEquals('testing-realm', $realm->getRealm());

        static::assertCount(2, $this->keycloak->realms()->all());
    }

    public function test_can_clear_caches(): void
    {
        $this->expectNotToPerformAssertions();

        $realm = new Realm(realm: 'master');

        $this->getKeycloak()->realms()->clearKeysCache($realm->getRealm());
        $this->getKeycloak()->realms()->clearRealmCache($realm->getRealm());
        $this->getKeycloak()->realms()->clearUserCache($realm->getRealm());
    }

    public function test_can_clear_keys_cache(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->realms()->clearKeysCache('master');
    }

    public function test_can_clear_realm_cache(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->realms()->clearRealmCache('master');
    }

    public function test_can_clear_user_cache(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->realms()->clearUserCache('master');
    }

    public function test_can_get_admin_events(): void
    {
        $adminEvents = $this->getKeycloak()->realms()->adminEvents('master');

        static::assertEmpty($adminEvents);
    }

    public function test_can_delete_admin_events(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->realms()->deleteAdminEvents('master');
    }

    public function test_can_update_realm_attributes(): void
    {
        $realm = $this->getKeycloak()->realms()->get(realm: 'master');

        static::assertFalse($realm->getAttributes()->contains('termsUrl'));

        $realm = $realm->withAttributes([
            'termsUrl' => 'https://example.com/terms',
        ]);

        $this->getKeycloak()->realms()->update($realm->getRealm(), $realm);

        $updatedRealm = $this->getKeycloak()->realms()->get(realm: $realm->getRealm());

        static::assertEquals('https://example.com/terms', $updatedRealm->getAttributes()->get('termsUrl'));
    }
}
