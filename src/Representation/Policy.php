<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ResourceCollection;
use Overtrue\Keycloak\Collection\ScopeCollection;
use Overtrue\Keycloak\Enum\DecisionStrategy;
use Overtrue\Keycloak\Enum\Logic;
use Overtrue\Keycloak\Type\Map;

/**
 * @method Map|null getConfig()
 * @method self withConfig(?Map $config)
 * @method DecisionStrategy|null getDecisionStrategy()
 * @method self withDecisionStrategy(?DecisionStrategy $decisionStrategy)
 * @method string|null getDescription()
 * @method self withDescription(?string $description)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method Logic|null getLogic()
 * @method self withLogic(?Logic $logic)
 * @method string|null getName()
 * @method self withName(?string $name)
 * @method string|null getOwner()
 * @method self withOwner(?string $owner)
 * @method string[]|null getPolicies()
 * @method self withPolicies(?string[] $policies)
 * @method string[]|null getResources()
 * @method self withResources(?string[] $resources)
 * @method ResourceCollection|null getResourcesData()
 * @method self withResourcesData(?ResourceCollection $resourcesData)
 * @method string[]|null getScopes()
 * @method self withScopes(?string[] $scopes)
 * @method ScopeCollection|null getScopesData()
 * @method self withScopesData(?ScopeCollection $scopesData)
 * @method string|null getType()
 * @method self withType(?string $type)
 *
 * @codeCoverageIgnore
 */
class Policy extends Representation
{
    public function __construct(
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $config = null,
        protected ?DecisionStrategy $decisionStrategy = null,
        protected ?string $description = null,
        protected ?string $id = null,
        protected ?Logic $logic = null,
        protected ?string $name = null,
        protected ?string $owner = null,
        /** @var string[]|null */
        protected ?array $policies = null,
        /** @var string[]|null */
        protected ?array $resources = null,
        protected ?ResourceCollection $resourcesData = null,
        /** @var string[]|null */
        protected ?array $scopes = null,
        protected ?ScopeCollection $scopesData = null,
        protected ?string $type = null,
    ) {}
}
