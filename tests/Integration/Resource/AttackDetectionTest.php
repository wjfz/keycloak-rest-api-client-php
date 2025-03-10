<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Integration\Resource;

use Overtrue\Keycloak\Test\Integration\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class AttackDetectionTest extends TestCase
{
    use IntegrationTestBehaviour;

    private string $realm;

    private string $userId;

    protected function setUp(): void
    {
        $this->realm = 'master';
        $this->userId = '978df19a-7fa5-441d-a4b6-e5ae238e5c12';
    }

    public function test_can_clear_attack_detection(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->attackDetection()->clear($this->realm);
    }

    public function test_can_clear_attack_detection_for_user(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->attackDetection()->clearUser($this->realm, $this->userId);
    }

    public function test_can_get_attack_detection_for_user(): void
    {
        $this->expectNotToPerformAssertions();

        $this->getKeycloak()->attackDetection()->userStatus($this->realm, $this->userId);
    }
}
