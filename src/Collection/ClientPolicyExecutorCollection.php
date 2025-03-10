<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ClientPolicyExecutor;

/**
 * @extends Collection<ClientPolicyExecutor>
 *
 * @codeCoverageIgnore
 */
class ClientPolicyExecutorCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return ClientPolicyExecutor::class;
    }
}
