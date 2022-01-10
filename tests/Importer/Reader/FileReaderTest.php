<?php

namespace App\Tests\Importer\Reader;

use App\Importer\Reader\ReaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileReaderTest extends TestCase
{
    /**
     * Test loading file by correct filename
     * @throws \ReflectionException
     */
    public function testLoadExistingFile(): void
    {
        $reader = $this->createMockedInstance();
        $reader->load(__FILE__);
        //Needs Reflection API to call protected method
        $reflection = new \ReflectionObject($reader);
        $method = $reflection->getMethod('getFile');
        $method->setAccessible(true);
        $this->assertInstanceOf(\SplFileObject::class, $method->invoke($reader));
    }

    /**
     * Test loading not existing file
     */
    public function testLoadNotExistingFile(): void
    {
        $this->expectException(\App\Importer\Reader\ReaderException::class);
        $this->expectExceptionMessage('File does not exists notExistingFilename');
        $reader = $this->createMockedInstance();
        $reader->load('notExistingFilename');
    }

    /**
     * Test loading directory instead file
     */
    public function testLoadDirectory(): void
    {
        $this->expectException(\App\Importer\Reader\ReaderException::class);
        $this->expectExceptionMessage('Given path \'' . __DIR__ . '\' is directory');
        $reader = $this->createMockedInstance();
        $reader->load(__DIR__);
    }

    /**
     * Create mocked instance of abstract FileReader class
     * 
     * @return MockObject|ReaderInterface
     */
    private function createMockedInstance(): MockObject
    {
        return $this->getMockForAbstractClass(\App\Importer\Reader\FileReader::class);
    }
}
