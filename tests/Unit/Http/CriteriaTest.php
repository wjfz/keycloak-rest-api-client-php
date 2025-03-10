<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Test\Unit\Http;

use DateTimeImmutable;
use Overtrue\Keycloak\Http\Criteria;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stringable;

#[CoversClass(Criteria::class)]
class CriteriaTest extends TestCase
{
    public function test_can_create_empty_criteria(): void
    {
        $criteria = new Criteria;

        static::assertSame([], $criteria->jsonSerialize());
    }

    public function test_filters_out_null_criterion(): void
    {
        $criteria = new Criteria([
            'foo' => null,
        ]);

        static::assertSame([], $criteria->jsonSerialize());
    }

    public function test_can_create_criteria_with_bool_criterion(): void
    {
        $criteria = new Criteria([
            'bool' => true,
        ]);

        static::assertArrayHasKey('bool', $criteria->jsonSerialize());
        static::assertSame('true', $criteria->jsonSerialize()['bool']);
    }

    public function test_can_create_criteria_with_array_criterion(): void
    {
        $criteria = new Criteria([
            'array' => ['type-a', 'type-b'],
        ]);

        static::assertArrayHasKey('array', $criteria->jsonSerialize());
        static::assertSame(['type-a', 'type-b'], $criteria->jsonSerialize()['array']);
    }

    public function test_can_create_criteria_with_date_time_immutable_criterion(): void
    {
        $criteria = new Criteria([
            'dateTimeImmutable' => new DateTimeImmutable('2022-12-18'),
        ]);

        static::assertArrayHasKey('dateTimeImmutable', $criteria->jsonSerialize());
        static::assertSame('2022-12-18', $criteria->jsonSerialize()['dateTimeImmutable']);
    }

    public function test_can_create_criteria_with_stringable_criterion(): void
    {
        $criteria = new Criteria([
            'stringable' => new class implements Stringable
            {
                public function __toString(): string
                {
                    return 'criterion';
                }
            },
        ]);

        static::assertArrayHasKey('stringable', $criteria->jsonSerialize());
        static::assertSame('criterion', $criteria->jsonSerialize()['stringable']);
    }
}
