<?php

namespace App\Service\Config;

use App\Service\Config\DataType\ConfigInterface;

interface ConfigRepositoryInterface
{
    public function saveConfig(ConfigInterface $config): void;
    public function loadConfig(): ConfigInterface;
}
