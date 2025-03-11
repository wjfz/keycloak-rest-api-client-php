<?php

namespace Overtrue\Keycloak\Serializer;

use Overtrue\Keycloak\Representation\AttributesAwareInterface;
use Overtrue\Keycloak\Representation\Representation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class AttributeNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $normalizer) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof AttributesAwareInterface;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return float|int|bool|\ArrayObject|array<string, mixed>|string|null
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $attributes = $data['attributes'];

            foreach ($attributes as $key => $value) {
                $attributes[$key] = (array) $value;
            }

            $data['attributes'] = $attributes;
        }

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Representation::class => true,
        ];
    }
}
