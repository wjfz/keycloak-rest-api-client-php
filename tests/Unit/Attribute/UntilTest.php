<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Attribute;

use Overtrue\Keycloak\Attribute\Until;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Until::class)]
class UntilTest extends TestCase
{
    public function test_can_be_constructed_with_version(): void
    {
        $until = new Until('20.0.0');

        static::assertSame('20.0.0', $until->version);
    }
}
