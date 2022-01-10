<?php
declare(strict_types=1);

namespace App\Importer\Reader;

class Item
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var int
     */
    private int $index;

    /**
     * @var string|null 
     */
    private ?string $error;

    /**
     * Item constructor.
     * @param int $index
     * @param mixed $data
     * @param string|null $error
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

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     */
    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    /**
     * Is item read successfully
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->error === null;
    }
}
