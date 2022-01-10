<?php
declare(strict_types=1);

namespace App\Importer\Reader;

abstract class FileReader extends AbstractReader
{
    /**
     * Loaded file SPL object
     * @var \SplFileObject
     */
    private \SplFileObject $splFileObject;
    
    /**
     * @inheritDoc
     */
    public function load($source): void
    {
        if ($source instanceof \SplFileObject) {
            $this->splFileObject = $source;
            return;
        }
        try {
            $this->splFileObject = new \SplFileObject($source);
        } catch (\RuntimeException $re) {
            if (file_exists($source)) {
                throw new ReaderException("Can't open file " . $source);
            }
            throw new ReaderException("File does not exists " . $source);
        } catch (\LogicException $le) {
            throw new ReaderException("Given path '" . $source . "' is directory");
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
