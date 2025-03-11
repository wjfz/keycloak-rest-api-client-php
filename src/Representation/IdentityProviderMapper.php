<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;

/**
 * @method Map|null getConfig()
 * @method self withConfig(?Map $config)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method string|null getIdentityProviderAlias()
 * @method self withIdentityProviderAlias(?string $identityProviderAlias)
 * @method string|null getIdentityProviderMapper()
 * @method self withIdentityProviderMapper(?string $identityProviderMapper)
 * @method string|null getName()
 * @method self withName(?string $name)
 *
 * @codeCoverageIgnore
 */
class IdentityProviderMapper extends Representation
{
    protected ?StringMap $config = null;

    /**
     * @param StringMap|array<string, string>|null $config
     */
    public function __construct(
        StringMap|array|null $config = null,
        protected ?string $id = null,
        protected ?string $identityProviderAlias = null,
        protected ?string $identityProviderMapper = null,
        protected ?string $name = null,
    ) {
        $this->config = StringMap::make($config);
    }
}
