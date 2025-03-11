<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Collection;

use Overtrue\Keycloak\Representation\KeyMetadata;

/**
 * @extends Collection<KeyMetadata>
 *
 * @codeCoverageIgnore
 */
class KeyMetadataCollection extends Collection
{
    #[\Override]
    public static function getRepresentationClass(): string
    {
        return KeyMetadata::class;
    }
}
