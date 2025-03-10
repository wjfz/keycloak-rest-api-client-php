<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Json;

use Overtrue\Keycloak\Exception\JsonDecodeException;
use Overtrue\Keycloak\Json\JsonDecoder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonDecoder::class)]
class JsonDecoderTest extends TestCase
{
    private JsonDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new JsonDecoder;
    }

    public function test_can_decode(): void
    {
        self::assertSame(
            [
                'Hey' => 'I am a valid JSON string!',
            ],
            $this->decoder->decode('{"Hey": "I am a valid JSON string!"}'),
        );
    }

    public function test_throws_exception_on_malformed_json(): void
    {
        $this->expectException(JsonDecodeException::class);

        $this->decoder->decode('{3:abcd"}');
    }
}
