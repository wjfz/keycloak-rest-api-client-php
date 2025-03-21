<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ScopeCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Symfony\Component\Serializer\Attribute\SerializedName;

/**
 * @method ArrayMap getAttributes()
 * @method self withAttributes(ArrayMap|array|null $value)
 * @method string|null getDisplayName()
 * @method self withDisplayName(?string $value)
 * @method string|null getIconUri()
 * @method self withIconUri(?string $value)
 * @method string|null getId()
 * @method self withId(?string $value)
 * @method string|null getName()
 * @method self withName(?string $value)
 * @method bool|null getOwnerManagedAccess()
 * @method self withOwnerManagedAccess(?bool $value)
 * @method ScopeCollection|null getScopes()
 * @method self withScopes(?ScopeCollection $value)
 * @method string|null getType()
 * @method self withType(?string $value)
 * @method string[]|null getUris()
 * @method self withUris(?array $value)
 *
 * @codeCoverageIgnore
 */
class Resource extends Representation
{
    protected ArrayMap $attributes;

    /**
     * @param  ArrayMap|array<string, string|string[]>|null  $attributes
     */
    public function __construct(
        #[SerializedName('_id')]
        protected ?string $id = null,
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
