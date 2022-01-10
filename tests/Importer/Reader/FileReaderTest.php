<?php

namespace App\Tests\Importer\Reader;

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
        $reader = $this->createMockedInstance(__FILE__);
        $reader->load();
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
        $reader = $this->createMockedInstance('notExistingFilename');
        $reader->load();
    }

    /**
     * Test loading directory instead file
     */
    public function testLoadDirectory(): void
    {
        $this->expectException(\App\Importer\Reader\ReaderException::class);
        $this->expectExceptionMessage('Given path \'' . __DIR__ . '\' is directory');
        $reader = $this->createMockedInstance(__DIR__);
        $reader->load();
    }

    /**
     * Create mocked instance of abstract FileReader class
     * @param string $filename
     * @return MockObject
     */
    private function createMockedInstance(string $filename): MockObject
    {
        return $this->getMockForAbstractClass(\App\Importer\Reader\FileReader::class, [$filename]);
    }
}
