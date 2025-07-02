<?php

namespace App\Tests\Command;

use App\Command\CacheRefreshCommand;
use App\Service\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CacheRefreshCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $cacheManagerMock = $this->createMock(CacheManager::class);
        $cacheManagerMock->expects($this->once())->method('cache');

        $command = new CacheRefreshCommand($cacheManagerMock);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }
}
