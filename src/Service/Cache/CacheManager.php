<?php

namespace App\Service\Cache;

use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ConfigInterface;

class CacheManager
{
    public function __construct(private ChannelCacheRefresher $channelCacheRefresher, private ConfigInterface $config)
    {
    }

    public function cache(): void
    {
        foreach ($this->config->getChannels() as $channel) {
            $urlSource = $channel->getUrlSource();
            if ($urlSource instanceof ApiUrlSourceInterface) {
                $this->channelCacheRefresher->refresh($channel);
            }
        }
    }
}
