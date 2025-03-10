<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Http\CommandExecutor;
use Overtrue\Keycloak\Http\QueryExecutor;

/**
 * @codeCoverageIgnore
 */
abstract class Resource
{
    public function __construct(
        protected readonly CommandExecutor $commandExecutor,
        protected readonly QueryExecutor $queryExecutor,
    ) {}
}
