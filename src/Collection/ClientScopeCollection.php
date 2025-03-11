<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ClientScope;

/**
 * @extends Collection<ClientScope>
 *
 * @codeCoverageIgnore
 */
class ClientScopeCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return ClientScope::class;
    }
}
