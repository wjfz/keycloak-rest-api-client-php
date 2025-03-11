<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\PasswordPolicyType;

/**
 * @extends Collection<PasswordPolicyType>
 *
 * @codeCoverageIgnore
 */
class PasswordPolicyTypeCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return PasswordPolicyType::class;
    }
}
