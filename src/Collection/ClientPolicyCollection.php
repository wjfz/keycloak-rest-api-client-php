<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ClientPolicy;

/**
 * @extends Collection<ClientPolicy>
 *
 * @codeCoverageIgnore
 */
class ClientPolicyCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return ClientPolicy::class;
    }
}
