<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\Map;

/**
 * @method Map|null getAttributes()
 * @method self withAttributes(?Map $value)
 * @method bool|null getClientRole()
 * @method self withClientRole(?bool $value)
 * @method bool|null getComposite()
 * @method self withComposite(?bool $value)
 * @method RoleComposites|null getComposites()
 * @method self withComposites(?RoleComposites $value)
 * @method string|null getContainerId()
 * @method self withContainerId(?string $value)
 * @method string|null getDescription()
 * @method self withDescription(?string $value)
 * @method string|null getName()
 * @method self withName(?string $value)
 *
 * @codeCoverageIgnore
 */
class Role extends Representation implements AttributesAwareInterface
{
    use HasAttributes;

    public function __construct(
        /** @var Map|array<string, mixed>|null $attributes */
        protected Map|array|null $attributes = null,
        protected ?bool $clientRole = null,
        protected ?bool $composite = null,
        protected ?RoleComposites $composites = null,
        protected ?string $containerId = null,
        protected ?string $description = null,
        protected ?string $id = null,
        protected ?string $name = null,
    ) {}
}
