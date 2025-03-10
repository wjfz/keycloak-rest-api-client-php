<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\ContentType;
use Overtrue\Keycloak\Http\Criteria;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Test\Unit\Stub\Collection;
use Overtrue\Keycloak\Test\Unit\Stub\Representation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Command::class)]
class CommandTest extends TestCase
{
    public function test_has_no_payload_by_default(): void
    {
        static::assertNull((new Command('/path', Method::POST))->getPayload());
    }

    public function test_can_get_representation_payload(): void
    {
        $representation = new Representation;

        static::assertSame(
            $representation,
            (new Command('/path', Method::POST, [], $representation))->getPayload(),
        );
    }

    public function test_can_get_collection_payload(): void
    {
        $payload = new Collection([new Representation]);

        static::assertSame(
            $payload,
            (new Command('/path', Method::POST, [], $payload))->getPayload(),
        );
    }

    public function test_substitutes_parameters_in_path(): void
    {
        static::assertSame(
            '/admin/realms/master/groups/group-uuid',
            (new Command(
                '/admin/realms/{realm}/groups/{groupId}',
                Method::GET,
                [
                    'realm' => 'master',
                    'groupId' => 'group-uuid',
                ],
            ))->getPath(),
        );
    }

    public function test_supports_any_method(): void
    {
        foreach (Method::cases() as $method) {
            static::assertSame(
                $method->value,
                (new Command('/path', $method))->getMethod()->value,
            );
        }
    }

    public function test_builds_path_with_query_if_criteria_is_provided(): void
    {
        static::assertSame(
            '/admin/realms/master/users/user-uuid/execute-actions-email?client_id=foo&lifespan=600',
            (new Command(
                '/admin/realms/{realm}/users/{userId}/execute-actions-email',
                Method::PUT,
                [
                    'realm' => 'master',
                    'userId' => 'user-uuid',
                ],
                ['UPDATE_PASSWORD'],
                new Criteria([
                    'client_id' => 'foo',
                    'lifespan' => 600,
                ]),
            ))->getPath(),
        );
    }

    public function test_content_type_defaults_to_json(): void
    {
        $command = new Command('/path', Method::GET);

        static::assertSame(ContentType::JSON, $command->getContentType());
    }

    public function test_content_type_can_be_set_to_form_params(): void
    {
        $command = new Command('/path', Method::GET, contentType: ContentType::FORM_PARAMS);

        static::assertSame(ContentType::FORM_PARAMS, $command->getContentType());
    }
}
