<?php
namespace App\Service;

use App\Importer\Reader\CsvReader;
use App\Importer\Reader\Item;
use App\Importer\Reader\ReaderInterface;
use App\Importer\Result;
use App\Importer\Writer\DoctrineWriter;
use App\Importer\Writer\WriterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
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
     * ImporterService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface  $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Import data from CSV file
     *
     * @param string $filename                         Path to CSV file
     * @param string $type                             Target entity class name
     * @param DenormalizerInterface|null $denormalizer Denormalizer interface implementation
     * @param array|null $context                      Context options
     *
     * @return Result
     *
     * @throws ExceptionInterface
     * @throws \App\Importer\Reader\ReaderException
     * @throws \App\Importer\Writer\WriterException
     */
    public function importFromCsv(string $filename, string $type, ?DenormalizerInterface $denormalizer = null, ?array $context = []): Result
    {
        $result = new Result();
        //Initialize reader
        $reader = new CsvReader($filename, $result);
        $reader->configure($context);
        
        //Initialize writer
        $writer = new DoctrineWriter($this->entityManager);
        $writer->configure($context);
     
        if (!$denormalizer) {
            $denormalizer = new Serializer([
                new DateTimeNormalizer(),
                new ObjectNormalizer(null, null, null, new DoctrineExtractor($this->entityManager)),
            ]);
        }
        
        return $this->import($reader, $writer, $type, $denormalizer, $result, $context, CsvEncoder::FORMAT);
    }

    /**
     * Import data
     *
     * @param ReaderInterface $reader             Importer reader interface
     * @param WriterInterface $writer             Importer writer interface
     * @param string $type                        Entity class name
     * @param DenormalizerInterface $denormalizer Denormalizer interface implementation
     * @param Result $result                      Importer result object
     * @param array $context                      Context options
     * @param string|null $format                 Original data format (imported from what format)
     *
     * @return Result
     *
     * @throws ExceptionInterface
     * @throws \App\Importer\Reader\ReaderException
     * @throws \App\Importer\Writer\WriterException
     */
    private function import(ReaderInterface $reader, WriterInterface $writer, string $type, DenormalizerInterface $denormalizer, Result $result, array $context, ?string $format = null): Result
    {
        //Load data
        $reader->load();
        
        //Get validation groups from context options
        $groups = null;
        if (isset($context[self::OPTION_VALIDATION_GROUPS])) {
            $groups = $context[self::OPTION_VALIDATION_GROUPS];
        }
        
        //Get row by row from reader
        foreach ($reader->read() as $item) {
            try {
                $this->processItem($item, $type, $denormalizer, $writer, $result, $format, $groups);
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
        $entity = $denormalizer->denormalize($item->getData(), $type, $format);
        $errors = $this->validator->validate($entity, null, $groups);
        if (count($errors)) {
            $result->validationError($errors, $item);
        } else {
            $writer->write($entity);
        }
    }
}
