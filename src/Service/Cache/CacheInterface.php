<?php

namespace App\Service\Cache;

use App\Service\Config\DataType\ChannelInterface;

interface CacheInterface
{
    public function getChannel(): ChannelInterface;
    public function getStreamUrl(): string;
    public function setStreamUrl(string $streamUrl): void;
}
