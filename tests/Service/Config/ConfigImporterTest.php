<?php

namespace App\Tests\Service\Config;

use App\Exception\FileException;
use App\Service\Config\ConfigImporter;
use App\Service\Config\ConfigRepositoryInterface;
use App\Service\Config\DataType\ConfigInterface;
use App\Service\Config\Loader\ConfigLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ConfigImporterTest extends TestCase
{
    public function testImportFromNonExistingFileThrowsException(): void
    {
        $importer = $this->getSut();
        $this->expectExceptionObject(FileException::failedToReadYamlFile());
        $importer->import('foobar.yaml');
    }

    public function testImport(): void
    {
        $filename = sprintf('%s.yaml', uniqid());
        $configArray = ['foobar' => uniqid()];
        $yaml = sprintf('foobar: %s', $configArray['foobar']);

        $yamlFile = vfsStream::newFile($filename);
        $yamlFile->setContent($yaml);

        $vfsStream = vfsStream::setup();
        $vfsStream->addChild($yamlFile);

        $configStub = $this->createStub(ConfigInterface::class);
        $configLoaderMock = $this->createMock(ConfigLoader::class);
        $configLoaderMock->expects($this->once())->method('load')->with($configArray)->willReturn($configStub);

        $repositoryMock = $this->createMock(ConfigRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('saveConfig')->with($configStub);

        $importer = $this->getSut(configLoader: $configLoaderMock, configRepository: $repositoryMock);
        $importer->import($yamlFile->url());
    }

    private function getSut(
        ConfigLoader $configLoader = null,
        ConfigRepositoryInterface $configRepository = null,
    ): ConfigImporter {
        return new ConfigImporter(
            configLoader: $configLoader ?? $this->createStub(ConfigLoader::class),
            configRepository: $configRepository ?? $this->createStub(ConfigRepositoryInterface::class),
        );
    }
}
