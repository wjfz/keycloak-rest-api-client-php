<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Collection\FeatureCollection;
use Overtrue\Keycloak\Collection\PasswordPolicyTypeCollection;
use Overtrue\Keycloak\Type\ArrayMap;
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
 * @codeCoverageIgnore
 */
class ServerInfo extends Representation
{
    protected ?ArrayMap $builtinProtocolMappers = null;

    protected ?ArrayMap $clientInstallations = null;

    protected ?ArrayMap $componentTypes = null;

    protected ?ArrayMap $protocolMapperTypes = null;

    protected ?ArrayMap $providers = null;

    protected ?ArrayMap $themes = null;

    protected ?ArrayMap $enums = null;

    /**
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $builtinProtocolMappers
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $clientInstallations
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $componentTypes
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $enums
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $protocolMapperTypes
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $providers
     * @param \Overtrue\Keycloak\Type\ArrayMap|array<string, string|string[]>|null $themes
     */
    public function __construct(
        ArrayMap|array|null                     $builtinProtocolMappers = null,
        /** @var array<\Overtrue\Keycloak\Type\StringMap>|null */
        protected ?array                        $clientImporters = null,
        ArrayMap|array|null                     $clientInstallations = null,
        ArrayMap|array|null                     $componentTypes = null,
        #[Since('20.0.0')]
        protected ?CryptoInfo                   $cryptoInfo = null,
        ArrayMap|array|null                     $enums = null,
        #[Since('22.0.4')]
        protected ?FeatureCollection            $features = null,
        /** @var array<\Overtrue\Keycloak\Type\StringMap>|null */
        protected ?array                        $identityProviders = null,
        protected ?MemoryInfo                   $memoryInfo = null,
        protected ?PasswordPolicyTypeCollection $passwordPolicies = null,
        protected ?ProfileInfo                  $profileInfo = null,
        ArrayMap|array|null                     $protocolMapperTypes = null,
        ArrayMap|array|null                     $providers = null,
        /** @var array<\Overtrue\Keycloak\Type\StringMap>|null */
        protected ?array                        $socialProviders = null,
        protected ?SystemInfo                   $systemInfo = null,
        ArrayMap|array|null                     $themes = null,
    )
    {
        $this->builtinProtocolMappers = ArrayMap::make($builtinProtocolMappers);
        $this->clientInstallations = ArrayMap::make($clientInstallations);
        $this->componentTypes = ArrayMap::make($componentTypes);
        $this->protocolMapperTypes = ArrayMap::make($protocolMapperTypes);
        $this->providers = ArrayMap::make($providers);
        $this->themes = ArrayMap::make($themes);
        $this->enums = ArrayMap::make($enums);
    }
}
