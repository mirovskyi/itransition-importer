<?php

declare(strict_types=1);

namespace App\Importer\Writer;

use App\Model\EntityDTOInterface;
use Doctrine\Persistence\ObjectManager;

class DoctrineWriter implements WriterInterface
{
    private ObjectManager $entityManagement;

    private bool $testMode = false;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManagement = $entityManager;
    }

    public function configure(array $options): void
    {
        if (isset($options[WriterInterface::OPTION_TEST_MODE])) {
            $this->testMode = boolval($options[WriterInterface::OPTION_TEST_MODE]);
        }
    }

    public function write(object $item): void
    {
        if (!$this->testMode) {
            if ($item instanceof EntityDTOInterface) {
                $this->entityManagement->persist($item->createEntity());
            } else {
                $this->entityManagement->persist($item);
            }
        }
    }

    public function finish(): void
    {
        if (!$this->testMode) {
            $this->entityManagement->flush();
        }
    }
}
