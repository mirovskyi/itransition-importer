<?php
namespace App\Importer\Reader;

use App\Importer\Result;

class CsvReader extends FileReader
{
    /**
     * Available options for CSV reader
     */
    public const OPTION_DELIMITER       = 'delimiter';
    public const OPTION_ENCLOSURE       = 'enclosure';
    public const OPTION_ESCAPE          = 'escape';
    public const OPTION_HEADERS         = 'headers';
    public const OPTION_NO_HEADERS      = 'noHeaders';
    public const OPTION_SKIP_EMPTY_ROWS = 'skipEmptyRows';
    
    private const UTF8_BOM = "\xEF\xBB\xBF";

    /**
     * Default options
     * @var array 
     */
    private array $options = [
        self::OPTION_DELIMITER       => ',',
        self::OPTION_ENCLOSURE       => '"',
        self::OPTION_ESCAPE          => '\\',
        self::OPTION_HEADERS         => [],
        self::OPTION_NO_HEADERS      => false,
        self::OPTION_SKIP_EMPTY_ROWS => false
    ];

    /**
     * @var Result|null 
     */
    private ?Result $importerResult;

    /**
     * CsvReader constructor.
     * @param string $filename
     * @param Result|null $result
     */
    public function __construct(string $filename, ?Result $result = null)
    {
        parent::__construct($filename);
        $this->importerResult = $result;
    }

    /**
     * @inheritDoc
     * 
     * @throws ReaderException
     */
    public function configure(array $options): void
    {
        $this->options = array_merge($this->options, $options);
        if (!is_array($this->options[self::OPTION_HEADERS])) {
            throw new ReaderException('headers option for CSV reader should be array type');
        } 
    }

    /**
     * @inheritDoc
     */
    public function read(): \Traversable
    { 
        if ($this->getFile()->fread(strlen(self::UTF8_BOM)) !== self::UTF8_BOM) {
            $this->getFile()->rewind();
        }
        
        $delimiter = $this->options[self::OPTION_DELIMITER];
        $enclosure = $this->options[self::OPTION_ENCLOSURE];
        $escape    = $this->options[self::OPTION_ESCAPE];
        $headers   = $this->options[self::OPTION_HEADERS];
        $index     = 0;
        
        //Define headers
        if (!empty($headers) && !$this->options[self::OPTION_NO_HEADERS]) {
            //Skip header row
            $this->getFile()->fgets();
            $index++;
        } elseif (!$this->options[self::OPTION_NO_HEADERS]) {
            //Read header from file
            $headers = $this->getFile()->fgetcsv($delimiter, $enclosure, $escape);
            $index++;
        }
        
        //Read each line and yield simple array of values
        while ($row = $this->getFile()->fgetcsv($delimiter, $enclosure, $escape)) {
            //Skip empty row
            if ($this->options[self::OPTION_SKIP_EMPTY_ROWS] && count(array_filter($row, fn($val) => !empty(trim($val)))) == 0) {
                $index++;
                continue;
            }
            //Map row values with headers
            if (!empty($headers)) {
                if (count($headers) !== count($row)) {
                    if ($this->importerResult) {
                        $this->importerResult->exceptionError(new ReaderException('Wrong columns count in the row'), new Item($index, $row));
                        $this->importerResult->processed();
                    }
                    $index++;
                    continue;
                }
                $row = array_combine($headers, $row);
            }
            yield new Item($index++, $row);
        }
    }
}
