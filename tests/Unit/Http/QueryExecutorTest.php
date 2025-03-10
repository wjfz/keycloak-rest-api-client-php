<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use GuzzleHttp\Psr7\Response;
use Overtrue\Keycloak\Http\Client;
use Overtrue\Keycloak\Http\Method;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Http\QueryExecutor;
use Overtrue\Keycloak\Serializer\Serializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryExecutor::class)]
class QueryExecutorTest extends TestCase
{
    public function test_calls_client_with_query_properties(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(Method::GET->value, '/path/to/resource')
            ->willReturn(new Response(body: json_encode([], JSON_THROW_ON_ERROR)));

        $executor = new QueryExecutor($client, $this->createMock(Serializer::class));
        $executor->executeQuery(
            new Query(
                '/path/to/resource',
                'array',
            ),
        );
    }

    public function test_decodes_array_return_type(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(Method::GET->value, '/path/to/resource')
            ->willReturn(new Response(body: json_encode([], JSON_THROW_ON_ERROR)));

        $serializer = $this->createMock(Serializer::class);
        $serializer->expects(static::never())
            ->method('deserialize');

        $executor = new QueryExecutor($client, $serializer);
        $executor->executeQuery(
            new Query(
                '/path/to/resource',
                'array',
            ),
        );
    }

    public function test_deserializes_non_array_return_type(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects(static::once())
            ->method('request')
            ->with(Method::GET->value, '/path/to/resource')
            ->willReturn(new Response(body: json_encode([], JSON_THROW_ON_ERROR)));

        $serializer = $this->createMock(Serializer::class);
        $serializer->expects(static::once())
            ->method('deserialize')
            ->with(Client::class, '[]');

        $executor = new QueryExecutor($client, $serializer);
        $executor->executeQuery(
            new Query(
                '/path/to/resource',
                Client::class,
            ),
        );
    }
}
