<?php

namespace App\Service\Cache;

use App\Service\Config\DataType\ChannelInterface;

interface CacheRepositoryInterface
{
    public function findByChannel(ChannelInterface $channel): ?CacheInterface;

    public function insert(ChannelInterface $channel, string $streamUrl): void;
}
