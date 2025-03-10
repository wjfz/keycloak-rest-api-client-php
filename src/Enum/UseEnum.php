<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Enum;

enum UseEnum: string implements Enum
{
    case ENC = 'ENC';
    case SIG = 'SIG';
}
