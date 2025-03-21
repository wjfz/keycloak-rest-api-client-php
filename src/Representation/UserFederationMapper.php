<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\StringMap;

/**
 * @method StringMap getConfig()
 * @method self withConfig(StringMap|array|null $config)
 * @method string|null getFederationMapperType()
 * @method self withFederationMapperType(?string $federationMapperType)
 * @method string|null getFederationProviderDisplayName()
 * @method self withFederationProviderDisplayName(?string $federationProviderDisplayName)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method string|null getName()
 * @method self withName(?string $name)
 *
 * @codeCoverageIgnore
 */
class UserFederationMapper extends Representation
{
    protected StringMap $config;

    /** @param StringMap|array<string, string>|null $config */
    public function __construct(
        StringMap|array|null $config = null,
        protected ?string $federationMapperType = null,
        protected ?string $federationProviderDisplayName = null,
        protected ?string $id = null,
        protected ?string $name = null,
    ) {
        $this->config = StringMap::make($config);
    }
}
