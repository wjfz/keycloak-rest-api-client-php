<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ProtocolMapperCollection;
use Overtrue\Keycloak\Type\Map;

/**
 * @method string|null getDescription()
 * @method self withDescription(?string $description)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method string|null getName()
 * @method self withName(?string $name)
 * @method string|null getProtocol()
 * @method self withProtocol(?string $protocol)
 * @method ProtocolMapperCollection|null getProtocolMappers()
 * @method self withProtocolMappers(?ProtocolMapperCollection $protocolMappers)
 *
 * @codeCoverageIgnore
 */
class ClientScope extends Representation implements AttributesAwareInterface
{
    use HasAttributes;

    public function __construct(
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $attributes = null,
        protected ?string $description = null,
        protected ?string $id = null,
        protected ?string $name = null,
        protected ?string $protocol = null,
        protected ?ProtocolMapperCollection $protocolMappers = null,
    ) {}
}
