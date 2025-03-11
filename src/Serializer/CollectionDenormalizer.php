<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Serializer;

use Overtrue\Keycloak\Collection\Collection;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

readonly class CollectionDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     *
     * @throws \Overtrue\Keycloak\Exception\PropertyDoesNotExistException
     * @throws \ReflectionException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        /** @var Collection $collection */
        $collection = new $type;

        foreach ($data as $representation) {
            $collection->add(
                $this->denormalizer->denormalize($representation, $collection::getRepresentationClass(), $format, $context),
            );
        }

        return $collection;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, Collection::class);
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            Collection::class => true,
        ];
    }
}
