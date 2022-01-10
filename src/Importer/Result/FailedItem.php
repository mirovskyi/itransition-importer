<?php
declare(strict_types=1);

namespace App\Importer\Result;

use App\Importer\Reader\Item;

class FailedItem
{
    /**
     * Error types
     */
    const PROCESS_ERROR = 0;
    const VALIDATION_ERROR = 1;

    /**
     * Failed item data
     * @var Item 
     */
    private Item $item;

    /**
     * Error type
     * @var int 
     */
    private int $type;

    /**
     * Error message
     * @var array<string> 
     */
    private array $messages = [];

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array<string> $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @param string $message
     */
    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }
}
