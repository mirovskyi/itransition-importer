<?php

declare(strict_types=1);

namespace App\Service;

use App\Importer\ImporterException;
use App\Importer\Reader\ReaderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ImporterReaderLocator
{
    private ServiceLocator $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @throws ImporterException
     */
    public function getReader(string $format): ReaderInterface
    {
        $format = trim(strtolower($format));
        if (!$this->locator->has($format)) {
            throw new ImporterException(sprintf('Reader for "%s" format was not found', $format));
        }

        return $this->locator->get($format);
    }
}
