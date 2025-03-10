<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ClientPolicyCollection;

/**
 * @method ClientPolicyCollection|null getPolicies()
 * @method self withPolicies(?ClientPolicyCollection $policies)
 *
 * @codeCoverageIgnore
 */
class ClientPolicies extends Representation
{
    public function __construct(
        protected ?ClientPolicyCollection $policies = null,
    ) {}
}
