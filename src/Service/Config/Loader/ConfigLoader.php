<?php

namespace App\Service\Config\Loader;

use App\Exception\Config\BadConfigException;
use App\Service\Config\DataType\ConfigInterface;

class ConfigLoader
{
    public function __construct(
        private ConfigChannelLoader $channelLoader,
        private ConfigFactory $configFactory,
    ) {
    }

    public function load(array $configArray): ConfigInterface
    {
        if (!isset($configArray['channels']) || !is_array($configArray['channels'])) {
            throw BadConfigException::noChannelsArray();
        }

        if (empty($configArray['channels'])) {
            throw BadConfigException::noChannels();
        }

        $channels = [];
        foreach ($configArray['channels'] as $channel) {
            $channels[] = $this->channelLoader->load($channel);
        }

        return $this->configFactory->create($channels, $configArray['proxy'] ?? null);
    }
}
