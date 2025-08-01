[![codecov](https://codecov.io/gh/overtrue/keycloak-rest-api-client-php/graph/badge.svg?token=JSP1TB12UD)](https://codecov.io/gh/overtrue/keycloak-rest-api-client-php)
![PHP Analysis](https://github.com/overtrue/keycloak-rest-api-client-php/actions/workflows/php-analysis.yml/badge.svg?branch=main)
![PHP Unit](https://github.com/overtrue/keycloak-rest-api-client-php/actions/workflows/php-unit.yml/badge.svg?branch=main)
![PHP Integration (Keycloak compatibility)](https://github.com/overtrue/keycloak-rest-api-client-php/actions/workflows/php-integration.yml/badge.svg?branch=main)

# Keycloak Admin REST API Client

PHP client to interact with [Keycloak's Admin REST API](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html).

Inspired by [keycloak/keycloak-nodejs-admin-client](https://github.com/keycloak/keycloak-nodejs-admin-client).

> This is a fork of [fschmtt/keycloak-rest-api-client-php](https://github.com/fschmtt/keycloak-rest-api-client-php)

## Installation

Install via Composer:

```bash
composer require overtrue/keycloak-rest-api-client-php
```

## Usage

Example:

```php
$keycloak = new \Overtrue\Keycloak\Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin'
);

$serverInfo = $keycloak->serverInfo()->get();

echo sprintf(
    'Keycloak %s is running on %s/%s (%s) with %s/%s since %s and is currently using %s of %s (%s %%) memory.',
    $serverInfo->getSystemInfo()->getVersion(),
    $serverInfo->getSystemInfo()->getOsName(),
    $serverInfo->getSystemInfo()->getOsVersion(),
    $serverInfo->getSystemInfo()->getOsArchitecture(),
    $serverInfo->getSystemInfo()->getJavaVm(),
    $serverInfo->getSystemInfo()->getJavaVersion(),
    $serverInfo->getSystemInfo()->getUptime(),
    $serverInfo->getMemoryInfo()->getUsedFormated(),
    $serverInfo->getMemoryInfo()->getTotalFormated(),
    100 - $serverInfo->getMemoryInfo()->getFreePercentage(),
);
```

will print e.g.

```text
Keycloak 26.0.0 is running on Linux/5.10.25-linuxkit (amd64) with OpenJDK 64-Bit Server VM/11.0.11 since 0 days, 2 hours, 37 minutes, 7 seconds and is currently using 139 MB of 512 MB (28 %) memory.
```

More examples can be found in the [examples](examples) directory.

## Caching

The Keycloak client supports powerful caching based on PSR-16 (Simple Cache) standard:

```php
use DateInterval;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

$keycloak = new \Overtrue\Keycloak\Keycloak(
    baseUrl: 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
    cache: new Psr16Cache(new RedisAdapter($redis)),
    cacheConfig: [
        'prefix' => 'myapp_',
        'ttl' => [
            'version' => new DateInterval('PT6H'),
            'server_info' => new DateInterval('PT1H'),
            'access_token' => new DateInterval('PT1H'),
            'refresh_token' => new DateInterval('P1D'),
        ]
    ]
);
```

For detailed caching configuration and usage, see [CACHE.md](CACHE.md).

## Customization

### Custom representations & resources

You can register and use custom resources by providing your own representations and resources, e.g.:

```php
class MyCustomRepresentation extends \Overtrue\Keycloak\Representation\Representation
{
    public function __construct(
        protected ?string $id = null,
        protected ?string $name = null,
    ) {
    }
}

class MyCustomResource extends \Overtrue\Keycloak\Resource\Resource
{
    public function myCustomEndpoint(): MyCustomRepresentation
    {
        return $this->queryExecutor->executeQuery(
            new \Overtrue\Keycloak\Http\Query(
                '/my-custom-endpoint',
                MyCustomRepresentation::class,
            )
        );
    }
}
```

By extending the `Resource` class, you have access to both the `QueryExecutor` and `CommandExecutor`.
The `CommandExecutor` is designed to run state-changing commands against the server (without returning a response);
the `QueryExecutor` allows fetching resources and representations from the server.

To use your custom resource, pass the fully-qualified class name (FQCN) to the `Keycloak::resource()` method.
It provides you with an instance of your resource you can then work with:

```php
$keycloak = new Keycloak(
    $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    'admin',
    'admin',
);

$myCustomResource = $keycloak->resource(MyCustomResource::class);
$myCustomRepresentation = $myCustomResource->myCustomEndpoint();
```

## Available Resources

<!-- API_DOCS_START -->
### [Attack Detection](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_attack_detection)

| Endpoint | Response | API |
|----------|----------|-----|
| `DELETE /admin/realms/{realm}/attack-detection/brute-force/users` | ResponseInterface | [AttackDetection::clear()](src/Resource/AttackDetection.php) |
| `GET /admin/realms/{realm}/attack-detection/brute-force/users/{userId}` | [StringMap](src/Type/StringMap.php) | [AttackDetection::userStatus()](src/Resource/AttackDetection.php) |
| `DELETE /admin/realms/{realm}/attack-detection/brute-force/users/{userId}` | ResponseInterface | [AttackDetection::clearUser()](src/Resource/AttackDetection.php) |

### [Clients](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_clients)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms/{realm}/clients` | [ClientCollection](src/Collection/ClientCollection.php) | [Clients::all()](src/Resource/Clients.php) |
| `GET /admin/realms/{realm}/clients/{clientUuid}` | [ClientRepresentation](src/Representation/ClientRepresentation.php) | [Clients::get()](src/Resource/Clients.php) |
| `POST /admin/realms/{realm}/clients` | [ClientRepresentation](src/Representation/ClientRepresentation.php) | [Clients::import()](src/Resource/Clients.php) |
| `PUT /admin/realms/{realm}/clients/{clientUuid}` | [ClientRepresentation](src/Representation/ClientRepresentation.php) | [Clients::update()](src/Resource/Clients.php) |
| `DELETE /admin/realms/{realm}/clients/{clientUuid}` | ResponseInterface | [Clients::delete()](src/Resource/Clients.php) |
| `GET /admin/realms/{realm}/clients/{clientUuid}/user-sessions` | array | [Clients::getUserSessions()](src/Resource/Clients.php) |
| `GET /admin/realms/{realm}/clients/{clientUuid}/client-secret` | Credential | [Clients::getClientSecret()](src/Resource/Clients.php) |

### [Groups](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_clients)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms/{realm}/groups` | [GroupCollection](src/Collection/GroupCollection.php) | [Groups::all()](src/Resource/Groups.php) |
| `GET /admin/realms/{realm}/group-by-path/{path}` | [Group](src/Representation/Group.php) | [Groups::byPath()](src/Resource/Groups.php) |
| `GET /admin/realms/{realm}/groups/{groupId}/children` | [GroupCollection](src/Collection/GroupCollection.php) | [Groups::children()](src/Resource/Groups.php) |
| `GET /admin/realms/{realm}/groups/{groupId}/members` | [UserCollection](src/Collection/UserCollection.php) | [Groups::members()](src/Resource/Groups.php) |
| `GET /admin/realms/{realm}/groups/{groupId}` | [Group](src/Representation/Group.php) | [Groups::get()](src/Resource/Groups.php) |
| `POST /admin/realms/{realm}/groups` | [Group](src/Representation/Group.php) | [Groups::create()](src/Resource/Groups.php) |
| `POST /admin/realms/{realm}/groups/{groupId}/children` | [Group](src/Representation/Group.php) | [Groups::createChild()](src/Resource/Groups.php) |
| `PUT /admin/realms/{realm}/groups/{groupId}` | ResponseInterface | [Groups::update()](src/Resource/Groups.php) |
| `DELETE /admin/realms/{realm}/groups/{groupId}` | ResponseInterface | [Groups::delete()](src/Resource/Groups.php) |

### [Organizations](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_organizations)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms/{realm}/organizations` | [OrganizationCollection](src/Collection/OrganizationCollection.php) | [Organizations::all()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/{id}` | [Organization](src/Representation/Organization.php) | [Organizations::get()](src/Resource/Organizations.php) |
| `POST /admin/realms/{realm}/organizations` | [Organization](src/Representation/Organization.php) | [Organizations::create()](src/Resource/Organizations.php) |
| `PUT /admin/realms/{realm}/organizations/{id}` | [Organization](src/Representation/Organization.php) | [Organizations::update()](src/Resource/Organizations.php) |
| `DELETE /admin/realms/{realm}/organizations/{id}` | ResponseInterface | [Organizations::delete()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/{orgId}/members` | [MemberCollection](src/Collection/MemberCollection.php) | [Organizations::members()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/{orgId}/members/count` | int | [Organizations::membersCount()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/{orgId}/members/{memberId}` | Member | [Organizations::member()](src/Resource/Organizations.php) |
| `POST /admin/realms/{realm}/organizations/{orgId}/members` | ResponseInterface | [Organizations::addMember()](src/Resource/Organizations.php) |
| `DELETE /admin/realms/{realm}/organizations/{orgId}/members/{memberId}` | ResponseInterface | [Organizations::deleteMember()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/members/{memberId}/organizations` | [OrganizationCollection](src/Collection/OrganizationCollection.php) | [Organizations::memberOrganizations()](src/Resource/Organizations.php) |
| `GET /admin/realms/{realm}/organizations/{orgId}/members/{memberId}/organizations` | [OrganizationCollection](src/Collection/OrganizationCollection.php) | [Organizations::orgMemberOrganizations()](src/Resource/Organizations.php) |
| `POST /admin/realms/{realm}/organizations/{id}/members/invite-user` | ResponseInterface | [Organizations::inviteUser()](src/Resource/Organizations.php) |
| `POST /admin/realms/{realm}/organizations/{id}/members/invite-existing-user` | ResponseInterface | [Organizations::inviteExistingUser()](src/Resource/Organizations.php) |
| `POST /admin/realms/{realm}/organizations/{id}/identity-providers` | ResponseInterface | [Organizations::linkIdp()](src/Resource/Organizations.php) |
| `DELETE /admin/realms/{realm}/organizations/{id}/identity-providers/{alias}` | ResponseInterface | [Organizations::unlinkIdp()](src/Resource/Organizations.php) |

### [Realms Admin](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_realms_admin)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms` | [RealmCollection](src/Collection/RealmCollection.php) | [Realms::all()](src/Resource/Realms.php) |
| `GET /admin/realms/{realm}` | [Realm](src/Representation/Realm.php) | [Realms::get()](src/Resource/Realms.php) |
| `POST /admin/realms` | [Realm](src/Representation/Realm.php) | [Realms::import()](src/Resource/Realms.php) |
| `PUT /admin/realms/{realm}` | [Realm](src/Representation/Realm.php) | [Realms::update()](src/Resource/Realms.php) |
| `DELETE /admin/realms/{realm}` | ResponseInterface | [Realms::delete()](src/Resource/Realms.php) |
| `GET /admin/realms/{realm}/admin-events` | array | [Realms::adminEvents()](src/Resource/Realms.php) |
| `GET /admin/realms/{realm}/keys` | KeysMetadata | [Realms::keys()](src/Resource/Realms.php) |
| `DELETE /admin/realms/{realm}/admin-events` | ResponseInterface | [Realms::deleteAdminEvents()](src/Resource/Realms.php) |
| `POST /admin/realms/{realm}/clear-keys-cache` | ResponseInterface | [Realms::clearKeysCache()](src/Resource/Realms.php) |
| `POST /admin/realms/{realm}/clear-realm-cache` | ResponseInterface | [Realms::clearRealmCache()](src/Resource/Realms.php) |
| `POST /admin/realms/{realm}/clear-user-cache` | ResponseInterface | [Realms::clearUserCache()](src/Resource/Realms.php) |

### [Users](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_users)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms/{realm}/users` | [UserCollection](src/Collection/UserCollection.php) | [Users::all()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users/{userId}` | [UserRepresentation](src/Representation/UserRepresentation.php) | [Users::get()](src/Resource/Users.php) |
| `POST /admin/realms/{realm}/users` | [UserRepresentation](src/Representation/UserRepresentation.php) | [Users::create()](src/Resource/Users.php) |
| `PUT /admin/realms/{realm}/users/{userId}` | [UserRepresentation](src/Representation/UserRepresentation.php) | [Users::update()](src/Resource/Users.php) |
| `DELETE /admin/realms/{realm}/users/{userId}` | ResponseInterface | [Users::delete()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users` | [UserCollection](src/Collection/UserCollection.php) | [Users::search()](src/Resource/Users.php) |
| `PUT /admin/realms/{realm}/users/{userId}/groups/{groupId}` | ResponseInterface | [Users::joinGroup()](src/Resource/Users.php) |
| `DELETE /admin/realms/{realm}/users/{userId}/groups/{groupId}` | ResponseInterface | [Users::leaveGroup()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users/{userId}/groups` | [GroupCollection](src/Collection/GroupCollection.php) | [Users::retrieveGroups()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users/{userId}/role-mappings/realm` | [RoleCollection](src/Collection/RoleCollection.php) | [Users::retrieveRealmRoles()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users/{userId}/role-mappings/realm/available` | [RoleCollection](src/Collection/RoleCollection.php) | [Users::retrieveAvailableRealmRoles()](src/Resource/Users.php) |
| `POST /admin/realms/{realm}/users/{userId}/role-mappings/realm` | ResponseInterface | [Users::addRealmRoles()](src/Resource/Users.php) |
| `DELETE /admin/realms/{realm}/users/{userId}/role-mappings/realm` | ResponseInterface | [Users::removeRealmRoles()](src/Resource/Users.php) |
| `PUT /admin/realms/{realm}/users/{userId}/execute-actions-email` | ResponseInterface | [Users::executeActionsEmail()](src/Resource/Users.php) |
| `DELETE /admin/realms/{realm}/users/{userId}/federated-identity/{provider}` | ResponseInterface | [Users::removeFederatedIdentity()](src/Resource/Users.php) |
| `GET /admin/realms/{realm}/users/{userId}/credentials` | [CredentialCollection](src/Collection/CredentialCollection.php) | [Users::credentials()](src/Resource/Users.php) |

### [Roles](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_roles)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/realms/{realm}/roles` | [RoleCollection](src/Collection/RoleCollection.php) | [Roles::all()](src/Resource/Roles.php) |
| `GET /admin/realms/{realm}/roles/{roleName}` | [Role](src/Representation/Role.php) | [Roles::get()](src/Resource/Roles.php) |
| `POST /admin/realms/{realm}/roles` | [Role](src/Representation/Role.php) | [Roles::create()](src/Resource/Roles.php) |
| `DELETE /admin/realms/{realm}/roles/{roleName}` | ResponseInterface | [Roles::delete()](src/Resource/Roles.php) |
| `PUT /admin/realms/{realm}/roles/{roleName}` | [Role](src/Representation/Role.php) | [Roles::update()](src/Resource/Roles.php) |

### [Root](https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html#_root)

| Endpoint | Response | API |
|----------|----------|-----|
| `GET /admin/serverinfo` | [ServerInfoRepresentation](src/Representation/ServerInfoRepresentation.php) | [ServerInfo::get()](src/Resource/ServerInfo.php) |


<!-- API_DOCS_END -->

## Local development and testing

Run `docker compose up -d keycloak` to start a local Keycloak instance listening on http://localhost:8080.

Run your script (e.g. [examples/serverinfo.php](examples/serverinfo.php)) from within the `php` container:

```bash
docker compose run --rm php php examples/serverinfo.php
```

### Composer scripts

* `analyze`: Run phpstan analysis
* `fix`: Fix coding style issues (Laravel pint)
* `test`: Run unit and integration tests
* `test:unit`: Run unit tests
* `test:integration`: Run integration tests (requires a fresh and running Keycloak instance)
