<?php

declare(strict_types=1);

namespace App\Importer\Reader;

/**
 * Interface ReaderInterface.
 */
interface ReaderInterface
{
    /**
     * Get supported format name.
     */
    public static function getFormat(): string;

    /**
     * Reader context configuration.
     *
     * @param array<mixed> $options Configuration options
     */
    public function configure(array $options): void;

    /**
     * Run loading data process.
     * For example creating file resource for file readers or executing SQL expression for DB readers.
     *
     * @param mixed $source Source from where data must be loaded
     *
     * @throws ReaderException
     */
    public function load($source): void;

    /**
     * Iterate throw loaded data items.
     *
     * @return \Traversable<int, Item>
     *                                 Returns list of Item objects.
     *                                 The idea is to use Generator that yields row by row.
     *                                 This can avoid keeping big array of data in the memory.
     */
    public function read(): \Traversable;
}
