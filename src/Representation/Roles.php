<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\RealmCollection;
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
    public function __construct(
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $application = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $client = null,
        protected ?RealmCollection $realm = null,
    ) {}
}
