<?php

namespace App\Command;

use App\Exception\FileException;
use App\Service\Config\ConfigImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:config:import',
    description: 'Import config from file to database',
)]
class ConfigImportCommand extends Command
{
    public function __construct(private ConfigImporter $configImporter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('file');

        try {
            $this->configImporter->import($filename);
        } catch (FileException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        $io->success('Config imported');
        return Command::SUCCESS;
    }
}
