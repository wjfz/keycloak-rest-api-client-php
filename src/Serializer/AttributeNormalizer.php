<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Serializer;

use ArrayObject;
use Overtrue\Keycloak\Attribute\Since;
use Overtrue\Keycloak\Attribute\Until;
use Overtrue\Keycloak\Representation\Representation;
use ReflectionClass;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttributeNormalizer implements NormalizerInterface
{
    /**
     * @var array<class-string<Representation>, array<string, array{since?: string, until?: string}>>
     */
    private array $filteredProperties = [];

    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly ?string $keycloakVersion = null,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array<mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        $properties = $this->normalizer->normalize($object, $format, $context);

        if (! $this->keycloakVersion) {
            return $properties;
        }

        foreach ($this->getFilteredProperties($object) as $property => $versions) {
            if (isset($versions['since']) && (int) $this->keycloakVersion < (int) $versions['since']) {
                unset($properties[$property]);
            }

            if (isset($versions['until']) && (int) $this->keycloakVersion > (int) $versions['until']) {
                unset($properties[$property]);
            }
        }

        return $properties;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Representation;
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Representation::class => true,
        ];
    }

    /**
     * @return array<string, array{since?: string, until?: string}>
     */
    private function getFilteredProperties(Representation $representation): array
    {
        if (array_key_exists($representation::class, $this->filteredProperties)) {
            return $this->filteredProperties[$representation::class];
        }

        $properties = (new ReflectionClass($representation))->getProperties();

        $filteredProperties = [];

        foreach ($properties as $property) {
            $sinceAttribute = $property->getAttributes(Since::class);
            $untilAttribute = $property->getAttributes(Until::class);

            foreach ($sinceAttribute as $since) {
                $filteredProperties[$property->getName()]['since'] = $since->getArguments()[0];
            }

            foreach ($untilAttribute as $until) {
                $filteredProperties[$property->getName()]['until'] = $until->getArguments()[0];
            }
        }

        $this->filteredProperties[$representation::class] = $filteredProperties;

        return $filteredProperties;
    }
}
