<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Representation;

use Overtrue\Keycloak\Collection\ProtocolMapperCollection;
use Overtrue\Keycloak\Type\BooleanMap;
use Overtrue\Keycloak\Type\IntegerMap;
use Overtrue\Keycloak\Type\Map;
use Overtrue\Keycloak\Type\StringMap;

/**
 * @method BooleanMap|null getAccess()
 * @method self withAccess(BooleanMap|array|null $access)
 * @method StringMap|null getAttributes()
 * @method self withAttributes(StringMap|array|null $attributes)
 * @method string|null getAdminUrl()
 * @method self withAdminUrl(?string $adminUrl)
 * @method bool|null getAlwaysDisplayInConsole()
 * @method self withAlwaysDisplayInConsole(?bool $alwaysDisplayInConsole)
 * @method StringMap|null getAuthenticationFlowBindingOverrides()
 * @method self withAuthenticationFlowBindingOverrides(StringMap|array|null $authenticationFlowBindingOverrides)
 * @method bool|null getAuthorizationServicesEnabled()
 * @method self withAuthorizationServicesEnabled(?bool $authorizationServicesEnabled)
 * @method ResourceServer|null getAuthorizationSettings()
 * @method self withAuthorizationSettings(?ResourceServer $authorizationSettings)
 * @method string|null getBaseUrl()
 * @method self withBaseUrl(?string $baseUrl)
 * @method bool|null getBearerOnly()
 * @method self withBearerOnly(?bool $bearerOnly)
 * @method string|null getClientAuthenticatorType()
 * @method self withClientAuthenticatorType(?string $clientAuthenticatorType)
 * @method string|null getClientId()
 * @method self withClientId(?string $clientId)
 * @method bool|null getConsentRequired()
 * @method self withConsentRequired(?bool $consentRequired)
 * @method string[]|null getDefaultClientScopes()
 * @method self withDefaultClientScopes(?string[] $defaultClientScopes)
 * @method string|null getDescription()
 * @method self withDescription(?string $description)
 * @method bool|null getDirectAccessGrantsEnabled()
 * @method self withDirectAccessGrantsEnabled(?bool $directAccessGrantsEnabled)
 * @method bool|null getEnabled()
 * @method self withEnabled(?bool $enabled)
 * @method bool|null getFrontchannelLogout()
 * @method self withFrontchannelLogout(?bool $frontchannelLogout)
 * @method bool|null getFullScopeAllowed()
 * @method self withFullScopeAllowed(?bool $fullScopeAllowed)
 * @method string|null getId()
 * @method self withId(?string $id)
 * @method bool|null getImplicitFlowEnabled()
 * @method self withImplicitFlowEnabled(?bool $implicitFlowEnabled)
 * @method string|null getName()
 * @method self withName(?string $name)
 * @method int|null getNodeReRegistrationTimeout()
 * @method self withNodeReRegistrationTimeout(?int $nodeReRegistrationTimeout)
 * @method int|null getNotBefore()
 * @method self withNotBefore(?int $notBefore)
 * @method string[]|null getOptionalClientScopes()
 * @method self withOptionalClientScopes(?string[] $optionalClientScopes)
 * @method string|null getOrigin()
 * @method self withOrigin(?string $origin)
 * @method string|null getProtocol()
 * @method self withProtocol(?string $protocol)
 * @method ProtocolMapperCollection|null getProtocolMappers()
 * @method self withProtocolMappers(?ProtocolMapperCollection $protocolMappers)
 * @method bool|null getPublicClient()
 * @method self withPublicClient(?bool $publicClient)
 * @method string[]|null getRedirectUris()
 * @method self withRedirectUris(?string[] $redirectUris)
 * @method IntegerMap|null getRegisteredNodes()
 * @method self withRegisteredNodes(IntegerMap|array|null $registeredNodes)
 * @method string|null getRegistrationAccessToken()
 * @method self withRegistrationAccessToken(?string $registrationAccessToken)
 * @method string|null getRootUrl()
 * @method self withRootUrl(?string $rootUrl)
 * @method string|null getSecret()
 * @method self withSecret(?string $secret)
 * @method bool|null getServiceAccountsEnabled()
 * @method self withServiceAccountsEnabled(?bool $serviceAccountsEnabled)
 * @method bool|null getStandardFlowEnabled()
 * @method self withStandardFlowEnabled(?bool $standardFlowEnabled)
 * @method bool|null getSurrogateAuthRequired()
 * @method self withSurrogateAuthRequired(?bool $surrogateAuthRequired)
 * @method string[]|null getWebOrigins()
 * @method self withWebOrigins(?string[] $webOrigins)
 *
 * @codeCoverageIgnore
 */
class Client extends Representation
{
    protected ?BooleanMap $access = null;

    protected ?StringMap $attributes = null;

    protected ?StringMap $authenticationFlowBindingOverrides = null;

    protected ?IntegerMap $registeredNodes = null;

    /**
     * @param BooleanMap|array<string, bool>|null $access
     * @param StringMap|array<string, string|string[]>|null $attributes
     * @param StringMap|array<string, string|string[]>|null $authenticationFlowBindingOverrides
     * @param IntegerMap|array<string, int>|null $registeredNodes
     */
    public function __construct(
        BooleanMap|array|null $access = null,
        protected ?string $adminUrl = null,
        protected ?bool $alwaysDisplayInConsole = null,
        StringMap|array|null $attributes = null,
        StringMap|array|null $authenticationFlowBindingOverrides = null,
        protected ?bool $authorizationServicesEnabled = null,
        protected ?ResourceServer $authorizationSettings = null,
        protected ?string $baseUrl = null,
        protected ?bool $bearerOnly = null,
        protected ?string $clientAuthenticatorType = null,
        protected ?string $clientId = null,
        protected ?bool $consentRequired = null,
        /** @var string[]|null */
        protected ?array $defaultClientScopes = null,
        protected ?string $description = null,
        protected ?bool $directAccessGrantsEnabled = null,
        protected ?bool $enabled = null,
        protected ?bool $frontchannelLogout = null,
        protected ?bool $fullScopeAllowed = null,
        protected ?string $id = null,
        protected ?bool $implicitFlowEnabled = null,
        protected ?string $name = null,
        protected ?int $nodeReRegistrationTimeout = null,
        protected ?int $notBefore = null,
        /** @var string[]|null */
        protected ?array $optionalClientScopes = null,
        protected ?string $origin = null,
        protected ?string $protocol = null,
        protected ?ProtocolMapperCollection $protocolMappers = null,
        protected ?bool $publicClient = null,
        /** @var string[]|null */
        protected ?array $redirectUris = null,
        IntegerMap|array|null $registeredNodes = null,
        protected ?string $registrationAccessToken = null,
        protected ?string $rootUrl = null,
        protected ?string $secret = null,
        protected ?bool $serviceAccountsEnabled = null,
        protected ?bool $standardFlowEnabled = null,
        protected ?bool $surrogateAuthRequired = null,
        /** @var string[]|null */
        protected ?array $webOrigins = null,
    ) {
        $this->access = BooleanMap::make($access);
        $this->attributes = StringMap::make($attributes);
        $this->authenticationFlowBindingOverrides = StringMap::make($authenticationFlowBindingOverrides);
        $this->registeredNodes = IntegerMap::make($registeredNodes);
    }
}
