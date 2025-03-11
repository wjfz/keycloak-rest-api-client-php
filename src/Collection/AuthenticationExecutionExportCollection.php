<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\AuthenticationExecutionExport;

/**
 * @extends Collection<AuthenticationExecutionExport>
 *
 * @codeCoverageIgnore
 */
class AuthenticationExecutionExportCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return AuthenticationExecutionExport::class;
    }
}
