<?php

namespace App\Service\Config\Loader;

use App\Entity\Channel;
use App\Entity\Config;
use App\Service\Config\DataType\ConfigInterface;

class ConfigFactory
{
    /**
     * @param Channel[] $channels
     */
    public function create(array $channels, ?string $proxyUrl): ConfigInterface
    {
        return new Config($channels, $proxyUrl);
    }
}
