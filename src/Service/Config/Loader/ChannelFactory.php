<?php

namespace App\Service\Config\Loader;

use App\Entity\Channel;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\UrlSourceInterface;

class ChannelFactory
{
    public function create(string $tvgName, string $tvgId, UrlSourceInterface $urlSource): ChannelInterface
    {
        return new Channel($tvgName, $tvgId, $urlSource);
    }
}
