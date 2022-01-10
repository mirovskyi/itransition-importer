<?php
namespace App\Importer\Reader;

abstract class FileReader implements ReaderInterface
{
    /**
     * Loaded file SPL object
     * @var \SplFileObject
     */
    private \SplFileObject $splFileObject;

    /**
     * Path to file to be loaded
     * @var string
     */
    private string $filename;

    /**
     * FileReader constructor.
     * 
     * @param string $filename Path to the file
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
    
    /**
     * @inheritDoc
     */
    public function load(): void
    {
        try {
            $this->splFileObject = new \SplFileObject($this->filename);
        } catch (\RuntimeException $re) {
            if (file_exists($this->filename)) {
                throw new ReaderException("Can't open file " . $this->filename);
            }
            throw new ReaderException("File does not exists " . $this->filename);
        } catch (\LogicException $le) {
            throw new ReaderException("Given path '" . $this->filename . "' is directory");
        }
    }

    /**
     * Get SPL file object
     * 
     * @return \SplFileObject
     */
    protected function getFile(): \SplFileObject
    {
        return $this->splFileObject;
    }
}
