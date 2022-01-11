<?php

declare(strict_types=1);

namespace App\Tests\Importer\Writer;

use App\Entity\Product;
use App\Importer\Writer\DoctrineWriter;
use App\Importer\Writer\WriterInterface;
use App\Model\ProductDTO;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineWriterTest extends TestCase
{
    /**
     * Test saving product data via doctrine entity manager.
     *
     * @throws \App\Importer\Writer\WriterException
     */
    public function testCallEntityManagerMethods(): void
    {
        $em = $this->getMockedEntityManager();
        $em->expects($this->exactly(2))
            ->method('persist');
        $em->expects($this->once())
            ->method('flush');

        $writer = new DoctrineWriter($em);
        $productDTO = new ProductDTO();
        $productDTO->code = $productDTO->name = $productDTO->description = 'Test';
        $writer->write($productDTO);
        $writer->write(new Product('test', 'Test', 'Test'));
        $writer->finish();
    }

    /**
     * Test DoctrineWriter test mode configuration.
     *
     * @throws \App\Importer\Writer\WriterException
     */
    public function testConfigurationWithTestMode(): void
    {
        $em = $this->getMockedEntityManager();
        $em->expects($this->never())
            ->method('persist');
        $em->expects($this->never())
            ->method('flush');

        $writer = new DoctrineWriter($em);
        $writer->configure([WriterInterface::OPTION_TEST_MODE => true]);
        $writer->write(new Product('test', 'Test', 'Test'));
        $writer->finish();
    }

    /**
     * @return MockObject|ObjectManager
     */
    private function getMockedEntityManager()
    {
        return $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
