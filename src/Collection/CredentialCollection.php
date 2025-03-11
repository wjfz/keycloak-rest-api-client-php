<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Credential;

/**
 * @extends Collection<Credential>
 *
 * @codeCoverageIgnore
 */
class CredentialCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Credential::class;
    }
}
