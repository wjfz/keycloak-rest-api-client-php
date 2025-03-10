<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use Overtrue\Keycloak\Http\Client;
use Overtrue\Keycloak\Http\Command;
use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\ContentType;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Json\JsonEncoder;
use Overtrue\Keycloak\Serializer\Serializer;
use Overtrue\Keycloak\Test\Unit\Stub\Collection;
use Overtrue\Keycloak\Test\Unit\Stub\Representation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CommandExecutor::class)]
class CommandExecutorTest extends TestCase
{
    public function test_calls_client_without_body_if_command_has_no_representation(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(
                Method::DELETE->value,
                '/path/to/resource',
                [
                    'body' => null,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ],
            );

        $executor = new CommandExecutor($client, new Serializer);
        $executor->executeCommand(
            new Command(
                '/path/to/resource',
                Method::DELETE,
            ),
        );
    }

    public function test_calls_client_with_json_if_command_has_representation(): void
    {
        $command = new Command(
            '/path/to/resource',
            Method::PUT,
            [],
            new Representation,
        );
        $payload = $command->getPayload();
        static::assertInstanceOf(Representation::class, $payload);

        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(
                Method::PUT->value,
                '/path/to/resource',
                [
                    'body' => (new JsonEncoder)->encode($payload->jsonSerialize()),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ],
            );

        $executor = new CommandExecutor($client, new Serializer);
        $executor->executeCommand($command);
    }

    public function test_calls_client_with_body_if_command_has_collection(): void
    {
        $representation = new Representation;

        $command = new Command(
            '/path/to/resource',
            Method::PUT,
            [],
            new Collection([$representation]),
        );

        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(
                Method::PUT->value,
                '/path/to/resource',
                [
                    'body' => (new JsonEncoder)->encode([$representation->jsonSerialize()]),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ],
            );

        $executor = new CommandExecutor($client, new Serializer);
        $executor->executeCommand($command);
    }

    public function test_calls_client_with_form_params_if_command_form_param_content_type(): void
    {
        $command = new Command(
            '/path/to/resource',
            Method::PUT,
            [],
            $payload = ['UPDATE_PASSWORD', 'VERIFY_EMAIL'],
            contentType: ContentType::FORM_PARAMS,
        );

        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(
                Method::PUT->value,
                '/path/to/resource',
                [
                    'form_params' => $payload,
                ],
            );

        $executor = new CommandExecutor($client, new Serializer);
        $executor->executeCommand($command);
    }
}
