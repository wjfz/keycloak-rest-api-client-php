<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\Member;

/**
 * @extends Collection<Member>
 *
 * @codeCoverageIgnore
 */
class MemberCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return Member::class;
    }
}
