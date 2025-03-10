<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\RequiredActionProvider;

/**
 * @extends Collection<RequiredActionProvider>
 *
 * @codeCoverageIgnore
 */
class RequiredActionProviderCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return RequiredActionProvider::class;
    }
}
