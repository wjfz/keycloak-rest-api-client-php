<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;

/**
 * @method string|null getAlias()
 * @method self withAlias(?string $alias)
 * @method StringMap|null getConfig()
 * @method self withConfig(StringMap|array|null $config)
 * @method string|null getId()
 * @method self withId(?string $id)
 *
 * @codeCoverageIgnore
 */
class AuthenticatorConfig extends Representation
{
    protected ?StringMap $config = null;

    /**
     * @param  StringMap|array<string,string>|null  $config
     */
    public function __construct(
        protected ?string $alias = null,
        StringMap|array|null $config = null,
        protected ?string $id = null,
    ) {
        $this->config = StringMap::make($config);
    }
}
