<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

use Overtrue\Keycloak\Json\JsonDecoder;
use Overtrue\Keycloak\Serializer\Serializer;

class QueryExecutor
{
    public function __construct(
        private readonly Client $client,
        private readonly Serializer $serializer,
    ) {}

    public function executeQuery(Query $query): mixed
    {
        $response = $this->client->request(
            $query->getMethod()->value,
            $query->getPath(),
        );

        if ($query->getReturnType() === 'array') {
            return (new JsonDecoder)->decode($response->getBody()->getContents());
        }

        return $this->serializer->deserialize(
            $query->getReturnType(),
            $response->getBody()->getContents(),
        );
    }
}
