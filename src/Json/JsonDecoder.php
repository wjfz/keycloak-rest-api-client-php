<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Json;

use JsonException;
use Overtrue\Keycloak\Exception\JsonDecodeException;

/**
 * @internal
 */
class JsonDecoder
{
    /**
     * @return array<mixed>
     *
     * @throws JsonDecodeException
     */
    public function decode(string $json): array
    {
        try {
            return json_decode(json: $json, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonDecodeException(previous: $e);
        }
    }
}
