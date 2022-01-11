<?php

declare(strict_types=1);

namespace App\Importer\Writer;

use Doctrine\Persistence\ObjectManager;

class DoctrineWriter implements WriterInterface
{
    private ObjectManager $entityManagement;

    private bool $testMode = false;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManagement = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(array $options): void
    {
        if (isset($options[WriterInterface::OPTION_TEST_MODE])) {
            $this->testMode = boolval($options[WriterInterface::OPTION_TEST_MODE]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function write(object $item): void
    {
        if (!$this->testMode) {
            $this->entityManagement->persist($item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function finish(): void
    {
        if (!$this->testMode) {
            $this->entityManagement->flush();
        }
    }
}
