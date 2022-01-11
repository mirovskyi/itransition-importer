<?php

declare(strict_types=1);

namespace App\Importer;

use App\Importer\Reader\Item;
use App\Importer\Result\FailedItem;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Result
{
    /**
     * Count of processed items.
     */
    private int $processed = 0;

    /**
     * @var array<FailedItem>
     */
    private array $failedItems = [];

    /**
     * Increments processed items.
     */
    public function processed(): void
    {
        ++$this->processed;
    }

    /**
     * Register validation error for item.
     */
    public function validationError(ConstraintViolationListInterface $errors, Item $item): void
    {
        $failedItem = new FailedItem();
        $failedItem->setType(FailedItem::VALIDATION_ERROR);
        $failedItem->setItem($item);
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $failedItem->addMessage($error->getPropertyPath().': '.$error->getMessage());
        }
        $this->failedItems[] = $failedItem;
    }

    /**
     * Register error for the item.
     */
    public function exceptionError(\Throwable $exception, Item $item): void
    {
        $failedItem = new FailedItem();
        $failedItem->setType(FailedItem::PROCESS_ERROR);
        $failedItem->setItem($item);
        $failedItem->setMessages([$exception->getMessage()]);
        $this->failedItems[] = $failedItem;
    }

    public function getProcessedItemsCount(): int
    {
        return $this->processed;
    }

    public function getFailedItemsCount(): int
    {
        return count($this->failedItems);
    }

    public function getSucceedItemCount(): int
    {
        return $this->getProcessedItemsCount() - $this->getFailedItemsCount();
    }

    /**
     * @return array<FailedItem>
     */
    public function getFailedItems(): array
    {
        return $this->failedItems;
    }
}
