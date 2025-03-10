<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\UserConsent;

/**
 * @extends Collection<UserConsent>
 *
 * @codeCoverageIgnore
 */
class UserConsentCollection extends Collection
{
    public static function getRepresentationClass(): string
    {
        return UserConsent::class;
    }
}
