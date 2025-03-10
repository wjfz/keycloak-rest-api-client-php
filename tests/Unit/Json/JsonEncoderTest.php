<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Json;

use Overtrue\Keycloak\Exception\JsonEncodeException;
use Overtrue\Keycloak\Json\JsonEncoder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonEncoder::class)]
class JsonEncoderTest extends TestCase
{
    private JsonEncoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new JsonEncoder;
    }

    public function test_can_encode(): void
    {
        self::assertSame(
            '{"Hey":"I am a valid JSON string!"}',
            $this->decoder->encode([
                'Hey' => 'I am a valid JSON string!',
            ]),
        );
    }

    public function test_throws_exception_on_malformed_json(): void
    {
        $this->expectException(JsonEncodeException::class);

        $this->decoder->encode(NAN);
    }
}
