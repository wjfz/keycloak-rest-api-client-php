<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Resource;

use DateInterval;
use Overtrue\Keycloak\Http\Query;
use Overtrue\Keycloak\Representation\ServerInfo as ServerInfoRepresentation;

class ServerInfo extends Resource
{
    public function get(): ServerInfoRepresentation
    {
        // If cache manager is available, use cache
        if ($this->cacheManager !== null) {
            $cacheKey = 'serverinfo';
            return $this->cacheManager->get($cacheKey, function() {
                return $this->queryExecutor->executeQuery(
                    new Query(
                        '/admin/serverinfo',
                        ServerInfoRepresentation::class,
                    ),
                );
            }, $this->cacheManager->getTtl('server_info', new DateInterval('PT1H')));
        }

        // Fallback to direct query
        return $this->queryExecutor->executeQuery(
            new Query(
                '/admin/serverinfo',
                ServerInfoRepresentation::class,
            ),
        );
    }

    /**
     * Clear ServerInfo cache
     */
    public function clearCache(): bool
    {
        if ($this->cacheManager === null) {
            return false;
        }

        return $this->cacheManager->delete('serverinfo');
    }
}
