<?php

declare(strict_types=1);

namespace App\Importer\Reader;

abstract class AbstractReader implements ReaderInterface
{
    /**
     * Supported reader format.
     */
    protected const SUPPORTED_FORMAT = 'unknown';

    /**
     * {@inheritDoc}
     */
    public static function getFormat(): string
    {
        return static::SUPPORTED_FORMAT;
    }
}
