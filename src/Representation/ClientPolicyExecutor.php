<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

/**
 * @method JsonNode|null getConfiguration()
 * @method self withConfiguration(?JsonNode $configuration)
 * @method string|null getExecutor()
 * @method self withExecutor(?string $executor)
 *
 * @codeCoverageIgnore
 */
class ClientPolicyExecutor extends Representation
{
    public function __construct(
        protected ?JsonNode $configuration = null,
        protected ?string $executor = null,
    ) {}
}
