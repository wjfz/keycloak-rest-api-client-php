<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Serializer;

use Overtrue\Keycloak\Type\Map;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class MapDenormalizer implements DenormalizerInterface
{
    /**
     * @param  array<string, mixed>  $context
     */
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($data instanceof Map) {
            return $data;
        }

        if (! is_array($data) || empty($data)) {
            return new Map;
        }

        return new Map($data);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Map::class;
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            Map::class => true,
        ];
    }
}
