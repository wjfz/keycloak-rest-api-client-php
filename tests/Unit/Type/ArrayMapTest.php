<?php

namespace Overtrue\Keycloak\Test\Unit\Type;

use Overtrue\Keycloak\Type\ArrayMap;
use PHPUnit\Framework\TestCase;

class ArrayMapTest extends TestCase
{
    public function test_flatten()
    {
        $map = new ArrayMap([
            'key-1' => ['value-1'],
            'key-2' => ['value-2'],
            'key-3' => ['value-3'],
        ]);

        $this->assertSame(
            [
                'key-1' => 'value-1',
                'key-2' => 'value-2',
                'key-3' => 'value-3',
            ],
            $map->flatten(),
        );
    }
}
