<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Client;

/**
 * @extends Collection<Client>
 *
 * @codeCoverageIgnore
 */
class ClientCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Client::class;
    }
}
