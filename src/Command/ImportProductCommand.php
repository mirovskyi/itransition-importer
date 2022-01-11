<?php

declare(strict_types=1);

namespace App\Command;

use App\Importer\Denormalizer\DiscontinuedDenormalizer;
use App\Importer\Reader\CsvReader;
use App\Importer\Result\FailedItem;
use App\Importer\Writer\WriterInterface;
use App\Service\ImporterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ImportProductCommand extends Command
{
    /**
     * The name of the bin/console command.
     *
     * @var string
     */
    protected static $defaultName = 'app:import-product';

    /**
     * Command description used in the --help output.
     *
     * @var string
     */
    protected static $defaultDescription = 'The command allows to import products data to the database';

    protected ImporterService $importerService;

    /**
     * ImportProductCommand constructor.
     */
    public function __construct(string $name = null, ImporterService $importerService)
    {
        parent::__construct($name);
        $this->importerService = $importerService;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'Path to source file to be imported');
        $this->addOption('format', 'f', InputArgument::OPTIONAL, 'Source data format', CsvReader::getFormat());
        $this->addOption(CsvReader::OPTION_HEADERS, null, InputOption::VALUE_OPTIONAL,
            'Force headers list (coma separated). Will skip original headers in the file',
            ['code', 'name', 'description', 'stock', 'cost', 'discontinued']);
        $this->addOption(WriterInterface::OPTION_TEST_MODE, 't', InputOption::VALUE_NONE, 'Run in test mode (don\'t store data to the database)');
        $this->addOption(CsvReader::OPTION_DELIMITER, null, InputOption::VALUE_OPTIONAL, 'CSV delimiter character', ',');
        $this->addOption(CsvReader::OPTION_ENCLOSURE, null, InputOption::VALUE_OPTIONAL, 'CSV enclosure character (value wrapper)', '"');
        $this->addOption(CsvReader::OPTION_ESCAPE, null, InputOption::VALUE_OPTIONAL, 'CSV escape character', '\\');
        $this->addOption(CsvReader::OPTION_NO_HEADERS, null, InputOption::VALUE_NONE, 'If there is no headers in CSV file');
        $this->addOption(CsvReader::OPTION_SKIP_EMPTY_ROWS, null, InputOption::VALUE_NONE, 'Skipp empty rows');
        $this->addOption(ImporterService::OPTION_VALIDATION_GROUPS, 'g', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Groups for validation', ['import']);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serializer = new Serializer([
            new DiscontinuedDenormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
        ]);

        $section = $output->section();
        $section->writeln('Importing...');
        $result = $this->importerService->import(
            $input->getArgument('source'),
            $input->getOption('format'),
            \App\Entity\Product::class,
            $serializer,
            array_merge($input->getArguments(), $input->getOptions())
        );
        $section->clear();

        $output->writeln('==================================');
        $output->writeln('Processed rows count: '.$result->getProcessedItemsCount());
        $output->writeln('Succeed rows count: '.$result->getSucceedItemCount());
        $output->writeln('Failed rows count: '.$result->getFailedItemsCount());
        $output->writeln('==================================');

        if ($result->getFailedItemsCount() > 0) {
            $output->writeln('');
            $output->writeln('ERRORS');
            $output->writeln('==================================');
        }
        foreach ($result->getFailedItems() as $item) {
            $this->writeFailedItem($output, $item);
        }

        return Command::SUCCESS;
    }

    /**
     * Formatting failed item and writing into the output.
     */
    private function writeFailedItem(OutputInterface $output, FailedItem $failedItem): void
    {
        $strType = (FailedItem::PROCESS_ERROR === $failedItem->getType() ? 'ERROR' : 'VALIDATION ERROR');
        $line = $failedItem->getItem()->getIndex() + 1;
        $rowMessage = "Line $line, $strType: ".implode(', ', $failedItem->getItem()->getData());
        $output->writeln('<comment>'.$rowMessage.'</comment>');
        foreach ($failedItem->getMessages() as $message) {
            $output->writeln('  <error>'.$message.'</error>');
        }
        $output->writeln('');
    }
}
