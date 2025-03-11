<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\ArrayMap;

/**
 * @method ArrayMap|null getAttributes()
 * @method self withAttributes(ArrayMap|array|null $value)
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
class Role extends Representation
{
    protected ?ArrayMap $attributes = null;

    /**
     * @param ArrayMap|array<string, string|string[]>|null $attributes
     */
    public function __construct(
        ArrayMap|array|null $attributes = null,
        protected ?bool $clientRole = null,
        protected ?bool $composite = null,
        protected ?RoleComposites $composites = null,
        protected ?string $containerId = null,
        protected ?string $description = null,
        protected ?string $id = null,
        protected ?string $name = null,
    ) {
        $this->attributes = ArrayMap::make($attributes);
    }
}
