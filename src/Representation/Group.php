<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Collection\GroupCollection;
use Overtrue\Keycloak\Type\ArrayMap;
use Overtrue\Keycloak\Type\BooleanMap;

/**
 * @method BooleanMap getAccess()
 * @method self withAccess(BooleanMap|array|null $access)
 * @method ArrayMap getAttributes()
 * @method self withAttributes(ArrayMap|array|null $attributes)
 * @method ArrayMap getClientRoles()
 * @method self withClientRoles(ArrayMap|array|null $clientRoles)
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

    /**
     * @param  \Overtrue\Keycloak\Type\BooleanMap|array<string, bool>|null  $access
     * @param  \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null  $attributes
     * @param  \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null  $clientRoles
     */
    public function __construct(
        BooleanMap|array|null $access = null,
        ArrayMap|array|null $attributes = null,
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
