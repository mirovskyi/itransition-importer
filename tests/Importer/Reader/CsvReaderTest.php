<?php

namespace App\Tests\Importer\Reader;

use PHPUnit\Framework\TestCase;
use App\Importer\Reader\CsvReader;
use App\Importer\Result;

class CsvReaderTest extends TestCase
{
    /**
     * Test CSV reader configuration with invalid headers option
     */
    public function testInvalidHeadersOption(): void
    {
        $this->expectException(\App\Importer\Reader\ReaderException::class);
        $this->expectExceptionMessage('headers option for CSV reader should be array type');
        $reader = new CsvReader('test');
        $reader->configure([CsvReader::OPTION_HEADERS => 'invalid headers data type']);
    }

    /**
     * Test valid CSV with headers line
     * 
     * @throws \App\Importer\Reader\ReaderException
     */
    public function testValidCsvWithHeaders(): void
    {
        $filename = $this->createTempCsvFile($this->getValidCsvContent());
        $reader = new CsvReader($filename);
        $reader->load();
        $data = [];
        foreach ($reader->read() as $item) {
            $data[] = $item->getData();
        }
        $this->assertCount(2, $data);
        $this->assertEquals([
            ['header1'=>'test11','header2'=>'test12','header3'=>'test13'],
            ['header1'=>'test21','header2'=>'test22','header3'=>'test23']
        ], $data);
        unlink($filename);
    }

    /**
     * Test valid CSV with configured headers
     * 
     * @throws \App\Importer\Reader\ReaderException
     */
    public function testValidCsvWithHeadersOverride(): void
    {
        $filename = $this->createTempCsvFile($this->getValidCsvContent());
        $reader = new CsvReader($filename);
        $reader->configure([CsvReader::OPTION_HEADERS => ['h1','h2','h3']]);
        $reader->load();
        $data = [];
        foreach ($reader->read() as $item) {
            $data[] = $item->getData();
        }
        $this->assertCount(2, $data);
        $this->assertEquals([
            ['h1'=>'test11','h2'=>'test12','h3'=>'test13'],
            ['h1'=>'test21','h2'=>'test22','h3'=>'test23']
        ], $data);
        unlink($filename);
    }

    /**
     * Test valid CSV without headers (skip headers line)
     *
     * @throws \App\Importer\Reader\ReaderException
     */
    public function testValidCsvWithoutHeaders(): void
    {
        $filename = $this->createTempCsvFile($this->getValidCsvContent());
        $reader = new CsvReader($filename);
        $reader->configure([CsvReader::OPTION_NO_HEADERS => true]);
        $reader->load();
        $data = [];
        foreach ($reader->read() as $item) {
            $data[] = $item->getData();
        }
        $this->assertCount(3, $data);
        $this->assertEquals([
            ['header1','header2','header3'],
            ['test11','test12','test13'],
            ['test21','test22','test23']
        ], $data);
        unlink($filename);
    }

    /**
     * Test skipping lines when columns count does not equal to headers count
     * 
     * @throws \App\Importer\Reader\ReaderException
     */
    public function testInconsistencyBetweenHeadersAndContent(): void
    {
        $filename = $this->createTempCsvFile($this->getValidCsvContent());
        $result = new Result();
        $reader = new CsvReader($filename, $result);
        $reader->configure([CsvReader::OPTION_HEADERS => ['h1','h2','h3','h4']]);
        $reader->load();
        $data = [];
        foreach ($reader->read() as $item) {
            $data[] = $item->getData();
        }
        $this->assertTrue($result->getFailedItemsCount() === 2);
        $this->assertTrue($result->getSucceedItemCount() === 0);
        $this->assertCount(0, $data);
        $this->assertContains('Wrong columns count in the row', $result->getFailedItems()[0]->getMessages());
        $this->assertContains('Wrong columns count in the row', $result->getFailedItems()[1]->getMessages());
        unlink($filename);
    }

    /**
     * Valid CSV content with header line
     * 
     * @return string
     */
    private function getValidCsvContent(): string
    {
        return <<< EOT
header1,header2,header3
test11,test12,test13
test21,test22,test23
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
