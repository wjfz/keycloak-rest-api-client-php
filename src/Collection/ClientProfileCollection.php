<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\ClientProfile;

/**
 * @extends Collection<ClientProfile>
 *
 * @codeCoverageIgnore
 */
class ClientProfileCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return ClientProfile::class;
    }
}
