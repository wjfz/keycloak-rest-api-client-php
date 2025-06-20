<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

enum ContentType: string
{
    case JSON = 'application/json';
    case FORM_PARAMS = 'application/x-www-form-urlencoded';
}
