<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\ServerInfo as ServerInfoRepresentation;

class ServerInfo extends Resource
{
    public function get(): ServerInfoRepresentation
    {
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/serverinfo',
                ServerInfoRepresentation::class,
            ),
        );
    }
}
