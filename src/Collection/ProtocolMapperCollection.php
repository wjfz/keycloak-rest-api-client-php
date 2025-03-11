<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ProtocolMapper;

/**
 * @extends Collection<ProtocolMapper>
 *
 * @codeCoverageIgnore
 */
class ProtocolMapperCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return ProtocolMapper::class;
    }
}
