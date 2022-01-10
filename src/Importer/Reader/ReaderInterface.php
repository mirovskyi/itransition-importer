<?php
namespace App\Importer\Reader;

/**
 * Interface ReaderInterface
 * @package App\Importer\Reader
 */
interface ReaderInterface
{
    /**
     * Reader context configuration
     *
     * @param array $options
     *   Configuration options
     */
    public function configure(array $options): void;
    
    /**
     * Run loading data process.
     * For example creating file resource for file readers or executing SQL expression for DB readers.
     *
     * @throws ReaderException
     */
    public function load(): void;

    /**
     * Iterate throw loaded data items
     * 
     * @return \Traversable<int, Item>
     *   Returns list of Item objects.  
     *   The idea is to use Generator that yields row by row.
     *   This can avoid keeping big array of data in the memory. 
     */
    public function read(): \Traversable;
}
