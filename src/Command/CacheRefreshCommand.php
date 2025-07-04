<?php

namespace App\Command;

use App\Service\Cache\CacheManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cache:refresh',
    description: 'Refresh cache for channels from configuration',
)]
class CacheRefreshCommand extends Command
{
    public function __construct(private CacheManager $cacheManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cacheManager->cache();

        return Command::SUCCESS;
    }
}
