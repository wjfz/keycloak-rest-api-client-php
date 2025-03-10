<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Json;

use JsonException;
use Overtrue\Keycloak\Exception\JsonEncodeException;

/**
 * @internal
 */
class JsonEncoder
{
    /**
     * @throws JsonEncodeException
     */
    public function encode(mixed $data): string
    {
        try {
            return json_encode(value: $data, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonEncodeException(previous: $e);
        }
    }
}
