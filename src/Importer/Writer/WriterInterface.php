<?php
namespace App\Importer\Writer;

interface WriterInterface
{
    /**
     * Available context options
     */
    public const OPTION_TEST_MODE = 'test';
    
    /**
     * Writer context configuration
     * 
     * @param array $options
     *   Configuration options
     */
    public function configure(array $options): void;
    
    /**
     * Write next item data
     * 
     * @param object $item
     *   Object that represents item data
     * 
     * @throws WriterException
     */
    public function write(object $item): void;

    /**
     * Finish writing operation.
     * It can be used for commit all write operations doing before.
     * For example close file resource to save it or flush data to the database.
     *  
     * @throws WriterException
     */
    public function finish(): void;
}
