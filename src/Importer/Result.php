<?php
namespace App\Importer;

use App\Importer\Reader\Item;
use App\Importer\Result\FailedItem;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Result
{
    /**
     * Count of processed items
     * @var int 
     */
    private int $processed = 0;

    /**
     * List of failed items
     * @var array<FailedItem> 
     */
    private array $failedItems = [];

    /**
     * @var ConstraintViolationListNormalizer 
     */
    private ConstraintViolationListNormalizer $constraintViolationListNormalizer;

    /**
     * Result constructor.
     */
    public function __construct()
    {
        $this->constraintViolationListNormalizer = new ConstraintViolationListNormalizer();
    }

    /**
     * Process next item
     */
    public function processed(): void
    {
        $this->processed++;
    }

    /**
     * Register validation error for item
     *
     * @param ConstraintViolationListInterface $errors   List of validation constraints
     * @param Item $item                                 Failed item data
     */
    public function validationError(ConstraintViolationListInterface $errors, Item $item): void
    {
        $failedItem = new FailedItem();
        $failedItem->setType(FailedItem::VALIDATION_ERROR);
        $failedItem->setItem($item);
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $failedItem->addMessage($error->getPropertyPath() . ': ' . $error->getMessage());
        }
        $this->failedItems[] = $failedItem;
    }

    /**
     * Register error for the item 
     * 
     * @param \Throwable $exception Exception object
     * @param Item $item            Failed item data
     */
    public function exceptionError(\Throwable $exception, Item $item): void
    {
        $failedItem = new FailedItem();
        $failedItem->setType(FailedItem::PROCESS_ERROR);
        $failedItem->setItem($item);
        $failedItem->setMessages([$exception->getMessage()]);
        $this->failedItems[] = $failedItem;
    }

    /**
     * @return int
     */
    public function getProcessedItemsCount():int
    {
        return $this->processed;
    }

    /**
     * @return int
     */
    public function getFailedItemsCount(): int
    {
        return count($this->failedItems);
    }

    /**
     * @return int
     */
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
