<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\RealmCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\Map;

/**
 * @method ArrayMap|null getClient()
 * @method self withClient(ArrayMap|array|null $client)
 * @method RealmCollection|null getRealm()
 * @method self withRealm(?RealmCollection $realm)
 *
 * @codeCoverageIgnore
 */
class RoleComposites extends Representation
{
    protected ArrayMap|null $client = null;

    /**
     * @param ArrayMap|array<string, string|string[]>|null $client
     */
    public function __construct(
        ArrayMap|array|null $client = null,
        protected ?RealmCollection $realm = null,
    ) {
        $this->client = ArrayMap::make($client);
    }
}
