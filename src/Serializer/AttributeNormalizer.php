<?php

namespace Overtrue\Keycloak\Serializer;

use Overtrue\Keycloak\Representation\AttributesAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class AttributeNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $normalizer) {}

    /**
     * @param  array<string, mixed>  $context
     */
    #[\Override]
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
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->normalizer->normalize($data, $format, $context);

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $attributes = $data['attributes'];

            foreach ($attributes as $key => $value) {
                $attributes[$key] = (array) $value;
            }

            $data['attributes'] = $attributes;
        }

        return $data;
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            AttributesAwareInterface::class => true,
        ];
    }
}
