<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Query::class)]
class QueryTest extends TestCase
{
    public function test_enforces_get_method(): void
    {
        static::assertSame(
            Method::GET->value,
            (new Query('', ''))->getMethod()->value,
        );
    }

    public function test_substitutes_parameters_in_path(): void
    {
        static::assertSame(
            '/admin/realms/master/groups/group-uuid',
            (new Query(
                '/admin/realms/{realm}/groups/{groupId}',
                GroupCollection::class,
                [
                    'realm' => 'master',
                    'groupId' => 'group-uuid',
                ],
            ))->getPath(),
        );
    }

    public function test_get_return_type(): void
    {
        static::assertSame(
            GroupCollection::class,
            (new Query(
                '/admin/realms/{realm}/groups',
                GroupCollection::class,
                [
                    'realm' => 'master',
                ],
            ))->getReturnType(),
        );
    }

    public function test_builds_path_with_query_if_criteria_is_provided(): void
    {
        static::assertSame(
            '/admin/realms/master/groups?username=foo&exact=true',
            (new Query(
                '/admin/realms/{realm}/groups',
                GroupCollection::class,
                [
                    'realm' => 'master',
                ],
                new Criteria([
                    'username' => 'foo',
                    'exact' => true,
                ]),
            ))->getPath(),
        );
    }

    public function test_builds_path_without_query_if_criteria_is_not_provided(): void
    {
        static::assertSame(
            '/admin/realms/master/groups',
            (new Query(
                '/admin/realms/{realm}/groups',
                GroupCollection::class,
                [
                    'realm' => 'master',
                ],
            ))->getPath(),
        );
    }

    public function test_builds_query_with_array(): void
    {
        static::assertSame(
            '/admin/realms/master/groups?username=foo&exact=true',
            (new Query(
                '/admin/realms/{realm}/groups',
                GroupCollection::class,
                [
                    'realm' => 'master',
                ],
                [
                    'username' => 'foo',
                    'exact' => true,
                ],
            ))->getPath(),
        );
    }
}
