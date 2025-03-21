<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Collection\IdentityProviderCollection;
use Overtrue\Keycloak\Collection\OrganizationDomainCollection;
use Overtrue\Keycloak\Collection\UserCollection;
use Overtrue\Keycloak\Type\ArrayMap;

/**
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method string|null getName()
 * @method self withName(?string $name)
 * @method bool|null getEnabled()
 * @method self withEnabled(?bool $enabled)
 * @method string|null getDescription()
 * @method self withDescription(?string $description)
 * @method OrganizationDomainCollection|null getDomains()
 * @method self withDomains(?OrganizationDomainCollection $domains)
 * @method UserCollection|null getMembers()
 * @method self withMembers(?UserCollection $members)
 * @method IdentityProviderCollection|null getIdentityProviders()
 * @method self withIdentityProviders(?IdentityProviderCollection $identityProviders)
 * @method string|null getAlias()
 * @method self withAlias(?string $alias)
 * @method string|null getRedirectUrl()
 * @method self withRedirectUrl(?string $redirectUrl)
 * @method ArrayMap getAttributes()
 * @method self withAttributes(ArrayMap|array|null $attributes)
 *
 * @codeCoverageIgnore
 */
class Organization extends Representation
{
    protected ArrayMap $attributes;

    /**
     * @param  \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null  $attributes
     */
    public function __construct(
        protected ?string $id = null,
        protected ?string $name = null,
        protected ?bool $enabled = null,
        protected ?string $description = null,
        ArrayMap|array|null $attributes = null,
        protected ?OrganizationDomainCollection $domains = null,
        protected ?UserCollection $members = null,
        protected ?IdentityProviderCollection $identityProviders = null,
        #[Since('26.0.0')]
        protected ?string $alias = null,
        #[Since('26.0.0')]
        protected ?string $redirectUrl = null,
    ) {
        $this->attributes = ArrayMap::make($attributes);
    }
}
