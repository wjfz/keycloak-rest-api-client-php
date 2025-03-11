<?php

declare(strict_types=1);

use Overtrue\Keycloak\Keycloak;
use Overtrue\Keycloak\Representation\Role;

require_once __DIR__.'/../vendor/autoload.php';

$keycloak = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
);

$realm = 'master';

$role = new Role(
    attributes: [
        'foo' => 'bar',
    ],
    name: uniqid('role')
);

$response = $keycloak->roles()->create($realm, $role);

print_r($response->toArray());
