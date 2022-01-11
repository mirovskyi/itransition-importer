<?php

declare(strict_types=1);

namespace App\Importer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DiscontinuedDenormalizer implements DenormalizerInterface
{
    private const SUPPORTED_TYPES = [
        \DateTime::class => true,
    ];

    /**
     * Denormalize string 'yes' to current DateTime object.
     *
     * {@inheritDoc}
     * @param array<mixed> $context
     *
     * @return mixed
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (empty($data)) {
            return null;
        }
        if ('yes' === trim(strtolower($data))) {
            return new \DateTime();
        }

        //Fallback to standard datetime denormalizer
        $dateTimeNormalizer = new DateTimeNormalizer();

        return $dateTimeNormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return isset(self::SUPPORTED_TYPES[$type]);
    }
}
