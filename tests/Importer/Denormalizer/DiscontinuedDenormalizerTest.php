<?php

namespace App\Tests\Importer\Denormalizer;

use App\Importer\Denormalizer\DiscontinuedDenormalizer;
use PHPUnit\Framework\TestCase;

class DiscontinuedDenormalizerTest extends TestCase
{
    /**
     * Test supported data types for DiscontinuedDenormalizer, with valid data type
     */
    public function testSupportsDenormalizationTrue(): void
    {
        $denormalizer = new DiscontinuedDenormalizer();
        $this->assertTrue(
            $denormalizer->supportsDenormalization(new \DateTime(), \DateTime::class)
        );
    }

    /**
     * Test supported data types for DiscontinuedDenormalizer, with invalid data type
     */
    public function testSupportsDenormalizationFalse(): void
    {
        $denormalizer = new DiscontinuedDenormalizer();
        $this->assertFalse(
            $denormalizer->supportsDenormalization('test', 'string')
        );
    }

    /**
     * Test denormalization with valid string 'yes', should denormalize to \DateTime object with current date 
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalizeYesString(): void
    {
        $denormalizer = new DiscontinuedDenormalizer();
        $this->assertInstanceOf(\DateTime::class, $denormalizer->denormalize('yes', 'string'));
    }

    /**
     * Test denormalization is not case sensitive
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalizeNotCaseSensitive(): void
    {
        $denormalizer = new DiscontinuedDenormalizer();
        $this->assertInstanceOf(\DateTime::class, $denormalizer->denormalize('YeS', 'string'));
    }

    /**
     * Test denormalization with invalid string
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalizeInvalidValue(): void
    {
        $this->expectException(\Symfony\Component\Serializer\Exception\NotNormalizableValueException::class);
        $denormalizer = new DiscontinuedDenormalizer();
        $denormalizer->denormalize('test', 'string');
    }

    /**
     * Test denormalization with valid datetime string, should run standard denormalization of DateTimeNormalizer
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalizeDatetimeString(): void
    {
        $denormalizer = new DiscontinuedDenormalizer();
        $this->assertInstanceOf(\DateTimeInterface::class, $denormalizer->denormalize('2022-01-06 12:00:01', 'string'));
    }
}
