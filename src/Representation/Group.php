<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\BooleanMap;
use Overtrue\Keycloak\Type\Map;

/**
 * @method Map|null getAccess()
 * @method self withAccess(?Map $access)
 * @method Map|null getClientRoles()
 * @method self withClientRoles(?Map $clientRoles)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method string|null getName()
 * @method self withName(?string $name)
 * @method string|null getParentId()
 * @method self withParentId(?string $parentId)
 * @method string|null getPath()
 * @method self withPath(?string $path)
 * @method string[]|null getRealmRoles()
 * @method self withRealmRoles(?string[] $realmRoles)
 * @method int|null getSubGroupCount()
 * @method self withSubGroupCount(?int $subGroupCount)
 * @method GroupCollection|null getSubGroups()
 * @method self withSubGroups(?GroupCollection[] $subGroups)
 *
 * @codeCoverageIgnore
 */
class Group extends Representation
{
    protected ?BooleanMap $access = null;

    protected ?ArrayMap $attributes = null;

    protected ?ArrayMap $clientRoles = null;

    public function __construct(
        /** @var \Overtrue\Keycloak\Type\BooleanMap|array<string, bool>|null $access */
        BooleanMap|array|null $access = null,
        /** @var \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $attributes */
        ArrayMap|array|null $attributes = null,
        /** @var \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $clientRoles */
        ArrayMap|array|null $clientRoles = null,
        protected ?string $id = null,
        protected ?string $name = null,
        #[Since('23.0.0')]
        protected ?string $parentId = null,
        protected ?string $path = null,
        /** @var string[]|null */
        protected ?array $realmRoles = null,
        #[Since('23.0.0')]
        protected ?int $subGroupCount = null,
        protected ?GroupCollection $subGroups = null,
    ) {
        $this->access = BooleanMap::make($access);
        $this->attributes = ArrayMap::make($attributes);
        $this->clientRoles = ArrayMap::make($clientRoles);
    }
}
