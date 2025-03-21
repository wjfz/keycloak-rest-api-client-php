<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Type\StringMap;

/**
 * @method int|null getChangedSyncPeriod()
 * @method self withChangedSyncPeriod(?int $changedSyncPeriod)
 * @method StringMap getConfig()
 * @method self withConfig(StringMap|array|null $config)
 * @method string|null getDisplayName()
 * @method self withDisplayName(?string $displayName)
 * @method int|null getFullSyncPeriod()
 * @method self withFullSyncPeriod(?int $fullSyncPeriod)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method int|null getLastSync()
 * @method self withLastSync(?int $lastSync)
 * @method int|null getPriority()
 * @method self withPriority(?int $priority)
 * @method string|null getProviderName()
 * @method self withProviderName(?string $providerName)
 *
 * @codeCoverageIgnore
 */
class UserFederationProvider extends Representation
{
    protected StringMap $config;

    /** @param StringMap|array<string, string>|null $config */
    public function __construct(
        protected ?int $changedSyncPeriod = null,
        StringMap|array|null $config = null,
        protected ?string $displayName = null,
        protected ?int $fullSyncPeriod = null,
        protected ?string $id = null,
        protected ?int $lastSync = null,
        protected ?int $priority = null,
        protected ?string $providerName = null,
    ) {
        $this->config = StringMap::make($config);
    }
}
