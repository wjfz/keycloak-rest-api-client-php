<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

use DateTimeInterface;
use Stringable;

class Criteria
{
    /**
     * @var array<string, mixed>
     */
    private array $criteria;

    /**
     * @param  array<string, mixed>  $criteria
     */
    public function __construct(array $criteria = [])
    {
        $this->criteria = $criteria;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $formatted = [];

        foreach ($this->criteria as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_bool($value)) {
                $formatted[$key] = $value ? 'true' : 'false';

                continue;
            }

            if ($value instanceof DateTimeInterface) {
                $formatted[$key] = $value->format('Y-m-d');

                continue;
            }

            if ($value instanceof Stringable) {
                $formatted[$key] = (string) $value;

                continue;
            }

            $formatted[$key] = $value;
        }

        return $formatted;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
