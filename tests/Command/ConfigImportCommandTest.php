<?php

namespace App\Tests\Command;

use App\Exception\FileException;
use App\Service\Config\ConfigImporter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigImportCommandTest extends KernelTestCase
{
    private ?string $filename = null;

    protected function setUp(): void
    {
        $this->filename = sprintf('%s.yaml', uniqid());
        self::bootKernel();
    }

    public function testImportFromFile(): void
    {
        $configImporterMock = $this->createMock(ConfigImporter::class);
        $configImporterMock->expects($this->once())->method('import')->with($this->filename);

        self::$kernel->getContainer()->set(ConfigImporter::class, $configImporterMock);

        $commandTester = $this->executeCommand();
        $commandTester->assertCommandIsSuccessful();
    }

    public function testImportFromWrongFileFails(): void
    {
        $configImporterMock = $this->createMock(ConfigImporter::class);
        $configImporterMock->expects($this->once())->method('import')->with($this->filename)
            ->willThrowException(FileException::failedToReadYamlFile());

        self::$kernel->getContainer()->set(ConfigImporter::class, $configImporterMock);

        $commandTester = $this->executeCommand();
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

    private function executeCommand(): CommandTester
    {
        $application = new Application(self::$kernel);
        $command = $application->find('app:config:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['file' => $this->filename]);

        return $commandTester;
    }
}
