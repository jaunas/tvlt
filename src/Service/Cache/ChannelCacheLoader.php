<?php

namespace App\Service\Cache;

use App\Exception\CacheException;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ChannelInterface;

class ChannelCacheLoader
{
    public function __construct(
        private CacheRepositoryInterface $repository,
    ) {
    }

    public function load(ChannelInterface $channel): CacheInterface
    {
        if (!($channel->getUrlSource() instanceof ApiUrlSourceInterface)) {
            throw CacheException::urlSourceNotSupported($channel->getTvgName());
        }

        $cache = $this->repository->findByChannel($channel);
        if (!$cache) {
            throw CacheException::empty($channel->getTvgName());
        }

        return $cache;
    }
}
