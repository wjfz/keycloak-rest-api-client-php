<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Collection\FeatureCollection;
use Overtrue\Keycloak\Collection\PasswordPolicyTypeCollection;
use Overtrue\Keycloak\Type\Map;

/**
 * @method Map|null getBuiltinProtocolMappers()
 * @method Map|null getClientImporters()
 * @method Map|null getClientInstallations()
 * @method Map|null getComponentTypes()
 * @method CryptoInfo|null getCryptoInfo()
 * @method Map|null getEnums()
 * @method FeatureCollection|null getFeatures()
 * @method Map|null getIdentityProviders()
 * @method MemoryInfo|null getMemoryInfo()
 * @method PasswordPolicyType[]|null getPasswordPolicies()
 * @method Map|null getProtocolMapperTypes()
 * @method ProfileInfo|null getProfileInfo()
 * @method Map|null getProviders()
 * @method Map|null getSocialProviders()
 * @method SystemInfo|null getSystemInfo()
 * @method Map|null getThemes()
 *
 * @codeCoverageIgnore
 */
class ServerInfo extends Representation
{
    public function __construct(
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $builtinProtocolMappers = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $clientImporters = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $clientInstallations = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $componentTypes = null,
        #[Since('20.0.0')]
        protected ?CryptoInfo $cryptoInfo = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $enums = null,
        #[Since('22.0.4')]
        protected ?FeatureCollection $features = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $identityProviders = null,
        protected ?MemoryInfo $memoryInfo = null,
        protected ?PasswordPolicyTypeCollection $passwordPolicies = null,
        protected ?ProfileInfo $profileInfo = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $protocolMapperTypes = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $providers = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $socialProviders = null,
        protected ?SystemInfo $systemInfo = null,
        /** @var Map|array<string, mixed>|null */
        protected Map|array|null $themes = null,
    ) {}
}
