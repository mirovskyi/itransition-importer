<?php
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
     * Item constructor.
     * @param int $index
     * @param $data
     */
    public function __construct(int $index, $data) 
    {
        $this->index = $index;
        $this->data = $data;
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
}
