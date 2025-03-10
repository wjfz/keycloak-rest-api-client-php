<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

/**
 * @internal
 */
enum Method: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
