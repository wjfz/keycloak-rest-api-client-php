<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\Map;

/**
 * @method Map|null getApplication()
 * @method self withApplication(?Map $value)
 * @method Map|null getClient()
 * @method self withClient(?Map $value)
 * @method RealmCollection|null getRealm()
 * @method self withRealm(?RealmCollection $value)
 *
 * @codeCoverageIgnore
 */
class Roles extends Representation
{
    protected ?ArrayMap $application = null;

    protected ?ArrayMap $client = null;

    /**
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, array<\Overtrue\Keycloak\Representation\Role>>|null $application
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, array<\Overtrue\Keycloak\Representation\Role>>|null $client
     */
    public function __construct(
        ArrayMap|array|null $application = null,
        ArrayMap|array|null $client = null,
        protected ?RealmCollection $realm = null,
    ) {
        $this->application = ArrayMap::make($application);
        $this->client = ArrayMap::make($client);
    }
}
