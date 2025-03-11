<?php

declare(strict_types=1);

use Overtrue\Keycloak\Keycloak;
use Overtrue\Keycloak\Representation\Group;

require_once __DIR__.'/../vendor/autoload.php';

$keycloak = new Keycloak(
    baseUrl: $_SERVER['KEYCLOAK_BASE_URL'] ?? 'http://keycloak:8080',
    username: 'admin',
    password: 'admin',
);

$realm = 'master';

$group = new Group(
    name: 'foo',
);

$response = $keycloak->groups()->create($realm, $group);

var_dump($response->toArray());
