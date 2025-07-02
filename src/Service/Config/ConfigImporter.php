<?php

namespace App\Service\Config;

use App\Exception\FileException;
use App\Service\Config\Loader\ConfigLoader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigImporter
{
    public function __construct(
        private ConfigLoader $configLoader,
        private ConfigRepositoryInterface $configRepository,
    ) {
    }

    public function import(string $filePath): void
    {
        try {
            $configArray = Yaml::parseFile($filePath);
        } catch (ParseException) {
            throw FileException::failedToReadYamlFile();
        }

        $config = $this->configLoader->load($configArray);
        $this->configRepository->saveConfig($config);
    }
}
