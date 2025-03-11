<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ScopeCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Symfony\Component\Serializer\Attribute\SerializedName;

/**
 * @codeCoverageIgnore
 */
class Resource extends Representation
{
    protected ?ArrayMap $attributes = null;

    public function __construct(
        #[SerializedName('_id')]
        protected ?string $id = null,
        /** @var ArrayMap|array<string, string|string[]>|null $attributes */
        ArrayMap|array|null $attributes = null,
        protected ?string $displayName = null,
        protected ?string $icon_uri = null,
        protected ?string $name = null,
        protected ?bool $ownerManagedAccess = null,
        protected ?ScopeCollection $scopes = null,
        protected ?string $type = null,
        /** @var string[]|null */
        protected ?array $uris = null,
    ) {
        $this->attributes = ArrayMap::make($attributes);
    }
}
