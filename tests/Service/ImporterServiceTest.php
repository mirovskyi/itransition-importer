<?php

namespace App\Tests\Service;

use App\Importer\Denormalizer\DiscontinuedDenormalizer;
use App\Importer\Reader\CsvReader;
use App\Importer\Result\FailedItem;
use App\Service\ImporterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImporterServiceTest extends KernelTestCase
{
    /**
     * Test import process from CSV file
     * 
     * @throws \App\Importer\Reader\ReaderException
     * @throws \App\Importer\Writer\WriterException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testCsvImporter(): void
    {
        self::bootKernel();
        
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $validator = $container->get(ValidatorInterface::class);
        
        $filename = $this->createTempCsvFile($this->getValidCsvContent());
        $serializer = new Serializer([
            new DiscontinuedDenormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor())
        ]);
        
        $importerService = new ImporterService($em, $validator);
        $result = $importerService->importFromCsv($filename, \App\Entity\Product::class, $serializer, [
            CsvReader::OPTION_HEADERS => ['strProductCode','strProductName','strProductDesc','intStock','numCost','dtmDiscontinued'],
            ImporterService::OPTION_VALIDATION_GROUPS => ['import']
        ]);

        $this->assertTrue($result->getProcessedItemsCount() === 6);
        $this->assertTrue($result->getSucceedItemCount() === 2);
        $this->assertTrue($result->getFailedItemsCount() === 4);
        $this->assertCount(4, $result->getFailedItems());
        $this->assertTrue($result->getFailedItems()[0]->getType() === FailedItem::PROCESS_ERROR);
        $this->assertTrue($result->getFailedItems()[1]->getType() === FailedItem::PROCESS_ERROR);
        $this->assertTrue($result->getFailedItems()[2]->getType() === FailedItem::VALIDATION_ERROR);
        $this->assertTrue($result->getFailedItems()[3]->getType() === FailedItem::VALIDATION_ERROR);
    }

    /**
     * Valid CSV content with header line
     *
     * @return string
     */
    private function getValidCsvContent(): string
    {
        return <<< EOT
Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued
P0001,TV,32” Tv,10,399.99,
P0002,Cd Player,Nice CD player,11,50.12,yes
P0003,Some name,Invalid column count,10
P0004,Some name2,Desc,invalid numeric format,23.33,
P0005,Test validation,Cost > 1000,10,1001,
P0006,Test validation2,Cost < 5 and Stock < 10,9,4,
EOT;
    }

    /**
     * Creates temp file with given content
     *
     * @param string $content Temp file content
     *
     * @return string Returns temp file name
     */
    private function createTempCsvFile(string $content): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filename, $content);

        return $filename;
    }
}
