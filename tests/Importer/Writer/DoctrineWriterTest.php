<?php

namespace App\Tests\Importer\Writer;

use App\Entity\Product;
use App\Importer\Writer\DoctrineWriter;
use App\Importer\Writer\WriterInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineWriterTest extends TestCase
{
    /**
     * Test saving product data via doctrine entity manager
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
        $writer->write(new Product());
        $writer->write(new Product());
        $writer->finish();
    }

    /**
     * Test DoctrineWriter test mode configuration
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
        $writer->write(new Product());
        $writer->finish();
    }

    /**
     * @return MockObject|ObjectManager
     */
    private function getMockedEntityManager(): MockObject
    {
        return $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
