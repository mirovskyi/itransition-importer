<?php

declare(strict_types=1);

namespace App\Importer\Reader;

class Item
{
    /**
     * @var mixed
     */
    private $data;

    private int $index;

    private ?string $error;

    /**
     * @param mixed $data
     */
    public function __construct(int $index, $data, ?string $error)
    {
        $this->index = $index;
        $this->data = $data;
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    /**
     * Is item read successfully.
     */
    public function isSuccess(): bool
    {
        return null === $this->error;
    }
}
