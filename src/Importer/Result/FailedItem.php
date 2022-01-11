<?php

declare(strict_types=1);

namespace App\Importer\Result;

use App\Importer\Reader\Item;

class FailedItem
{
    /**
     * Error types.
     */
    public const PROCESS_ERROR = 0;
    public const VALIDATION_ERROR = 1;

    /**
     * Failed item data.
     */
    private Item $item;

    /**
     * Error type.
     */
    private int $type;

    /**
     * Error message.
     *
     * @var array<string>
     */
    private array $messages = [];

    public function getItem(): Item
    {
        return $this->item;
    }

    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    public function getType(): int
    {
        return $this->type;
    }

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

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }
}
