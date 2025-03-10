<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Attribute;

use Overtrue\Keycloak\Attribute\Since;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Since::class)]
class SinceTest extends TestCase
{
    public function test_can_be_constructed_with_version(): void
    {
        $since = new Since('20.0.0');

        static::assertSame('20.0.0', $since->version);
    }
}
