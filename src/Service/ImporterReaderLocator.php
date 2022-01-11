<?php

declare(strict_types=1);

namespace App\Service;

use App\Importer\ImporterException;
use App\Importer\Reader\ReaderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ImporterReaderLocator
{
    private ServiceLocator $locator;

    /**
     * ImporterReaderLocator constructor.
     */
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Get reader implementation based on given format.
     *
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
