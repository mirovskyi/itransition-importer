<?php

declare(strict_types=1);

namespace App\Importer\Reader;

class CsvReader extends FileReader
{
    /**
     * {@inheritdoc}
     */
    protected const SUPPORTED_FORMAT = 'csv';

    /**
     * Available options for CSV reader.
     */
    public const OPTION_DELIMITER = 'csvDelimiter';
    public const OPTION_ENCLOSURE = 'csvEnclosure';
    public const OPTION_ESCAPE = 'csvEscape';
    public const OPTION_HEADERS = 'csvHeaders';
    public const OPTION_NO_HEADERS = 'csvNoHeaders';
    public const OPTION_SKIP_EMPTY_ROWS = 'csvSkipEmptyRows';

    private const UTF8_BOM = "\xEF\xBB\xBF";

    /**
     * Default options.
     */
    private const DEFAULT_OPTIONS = [
        self::OPTION_DELIMITER => ',',
        self::OPTION_ENCLOSURE => '"',
        self::OPTION_ESCAPE => '\\',
        self::OPTION_HEADERS => [],
        self::OPTION_NO_HEADERS => false,
        self::OPTION_SKIP_EMPTY_ROWS => false,
    ];

    /**
     * Context options.
     * @var array<mixed>
     */
    private array $options = self::DEFAULT_OPTIONS;

    /**
     * {@inheritDoc}
     *
     * @param array<mixed> $options
     *
     * @throws ReaderException
     */
    public function configure(array $options): void
    {
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
        if (is_string($this->options[self::OPTION_HEADERS])) {
            $this->options[self::OPTION_HEADERS] = explode(',', $this->options[self::OPTION_HEADERS]);
        }
        if (!is_array($this->options[self::OPTION_HEADERS])) {
            throw new ReaderException('headers option for CSV reader should be array or string type');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read(): \Traversable
    {
        if (self::UTF8_BOM !== $this->getFile()->fread(strlen(self::UTF8_BOM))) {
            $this->getFile()->rewind();
        }

        $delimiter = $this->options[self::OPTION_DELIMITER];
        $enclosure = $this->options[self::OPTION_ENCLOSURE];
        $escape = $this->options[self::OPTION_ESCAPE];
        $headers = $this->options[self::OPTION_HEADERS];
        $index = 0;

        //Define headers
        if (!empty($headers) && !$this->options[self::OPTION_NO_HEADERS]) {
            //Skip header row
            $this->getFile()->fgets();
            ++$index;
        } elseif (!$this->options[self::OPTION_NO_HEADERS]) {
            //Read header from file
            $headers = $this->getFile()->fgetcsv($delimiter, $enclosure, $escape);
            ++$index;
        }

        //Read each line and yield simple array of values
        while ($row = $this->getFile()->fgetcsv($delimiter, $enclosure, $escape)) {
            $error = null;
            //Skip empty row
            if ($this->options[self::OPTION_SKIP_EMPTY_ROWS] && 0 == count(array_filter($row, fn ($val) => !empty(trim($val))))) {
                ++$index;
                continue;
            }
            //Map row values with headers
            if (!empty($headers)) {
                if (count($headers) !== count($row)) {
                    $error = 'Wrong columns count in the row';
                } else {
                    $row = array_combine($headers, $row);
                }
            }
            yield new Item($index++, $row, $error);
        }
    }
}
