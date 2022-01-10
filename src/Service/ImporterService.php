<?php
declare(strict_types=1);

namespace App\Service;

use App\Importer\Reader\Item;
use App\Importer\Reader\ReaderException;
use App\Importer\Result;
use App\Importer\Writer\DoctrineWriter;
use App\Importer\Writer\WriterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImporterService
{
    //Importer context options
    const OPTION_VALIDATION_GROUPS = 'groups';
    
    /**
     * @var EntityManagerInterface 
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ValidatorInterface 
     */
    private ValidatorInterface $validator;

    /**
     * @var ImporterReaderLocator 
     */
    private ImporterReaderLocator $readerLocator;

    /**
     * ImporterService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param ImporterReaderLocator $readerLocator
     */
    public function __construct(EntityManagerInterface  $entityManager, ValidatorInterface $validator, ImporterReaderLocator  $readerLocator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->readerLocator = $readerLocator;
    }

    /**
     * Import data
     *
     * @param mixed $source Source from where data should be loaded
     * @param string $format Data format
     * @param string $type Entity class name
     * @param DenormalizerInterface|null $denormalizer Denormalizer interface implementation
     * @param array|null $context Context options
     *
     * @return Result
     *
     * @throws ExceptionInterface
     * @throws \App\Importer\Reader\ReaderException
     * @throws \App\Importer\Writer\WriterException
     * @throws \App\Importer\ImporterException
     */
    public function import($source, string $format, string $type, ?DenormalizerInterface $denormalizer = null, ?array $context = []): Result
    {
        //Get reader implementation
        $reader = $this->readerLocator->getReader($format);
        $reader->configure($context);
        //Create writer implementation
        $writer = new DoctrineWriter($this->entityManager);
        $writer->configure($context);
        
        //Load data
        $reader->load($source);
        
        //Get validation groups from context options
        $groups = null;
        if (isset($context[self::OPTION_VALIDATION_GROUPS])) {
            $groups = $context[self::OPTION_VALIDATION_GROUPS];
        }
        //Check denarmolizer and create default one if needed
        if (!$denormalizer) {
            $denormalizer = new Serializer([
                new DateTimeNormalizer(),
                new ObjectNormalizer(null, null, null, new DoctrineExtractor($this->entityManager)),
            ]);
        }
        
        $result = new Result();
        //Get row by row from reader
        foreach ($reader->read() as $item) {
            try {
                $this->processItem($item, $type, $denormalizer, $writer, $result, $reader->getFormat(), $groups);
            } catch (\Throwable $e) {
                $result->exceptionError($e, $item);
            } finally {
                $result->processed();
            }
        }
        
        $writer->finish();

        return $result;
    }

    /**
     * Process next item from reader.
     * Transforms data to entity object, validates and tries to write entity data. 
     * 
     * @param Item $item                                                      Item data
     * @param string $type                                                    Entity class name
     * @param DenormalizerInterface $denormalizer                             Denormalizer interface implementation
     * @param WriterInterface $writer                                         Importer writer interface implementation
     * @param Result $result                                                  Importer result object
     * @param string|null $format                                             Original data format (imported from what format)
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups   Validation groups
     * 
     * @throws \App\Importer\Writer\WriterException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function processItem(Item $item, string $type, DenormalizerInterface $denormalizer, WriterInterface $writer, Result $result, ?string $format = null, $groups = null): void
    {
        if ($item->isSuccess()) {
            $entity = $denormalizer->denormalize($item->getData(), $type, $format);
            $errors = $this->validator->validate($entity, null, $groups);
            if (count($errors)) {
                $result->validationError($errors, $item);
            } else {
                $writer->write($entity);
            }
        } else {
            $result->exceptionError(new ReaderException($item->getError()), $item);
        }
    }
}
