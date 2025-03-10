<?php

declare(strict_types=1);

use Overtrue\Keycloak\Collection\OrganizationDomainCollection;
use Overtrue\Keycloak\Keycloak;
use Overtrue\Keycloak\Representation\Organization;
use Overtrue\Keycloak\Representation\OrganizationDomain;
use Overtrue\Keycloak\Representation\Realm;

require_once __DIR__.'/../vendor/autoload.php';

$keycloak = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
);

$realm = 'master';

// enable organizations
$keycloak->realms()->update($realm, (new Realm(realm: $realm))->withOrganizationsEnabled(true));

$organization = new Organization(
    name: 'My-Organization',
    domains: new OrganizationDomainCollection([
        new OrganizationDomain('example.com'),
    ]),
);

$response = $keycloak->organizations()->create($realm, $organization);

var_dump($response);
