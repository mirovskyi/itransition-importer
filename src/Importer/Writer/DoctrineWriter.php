<?php
namespace App\Importer\Writer;

use Doctrine\Persistence\ObjectManager;

class DoctrineWriter implements WriterInterface
{
    /**
     * @var ObjectManager 
     */
    private ObjectManager $entityManagement;

    /**
     * @var bool 
     */
    private bool $testMode = false;

    /**
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManagement = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function configure(array $options): void
    {
        if (isset($options[WriterInterface::OPTION_TEST_MODE])) {
            $this->testMode = boolval($options[WriterInterface::OPTION_TEST_MODE]);
        }
    }

    /**
     * @inheritDoc
     */
    public function write(object $item): void
    {
        if (!$this->testMode) {
            $this->entityManagement->persist($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function finish(): void
    {
        if (!$this->testMode) {
            $this->entityManagement->flush();
        }
    }
}
