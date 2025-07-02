<?php

namespace App\Service\Cache;

use App\Exception\CacheException;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\HttpClient\RequestFactoryInterface;
use App\Service\JsonDecoderInterface;

class ChannelCacheRefresher
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private JsonDecoderInterface $jsonDecoder,
        private CacheRepositoryInterface $repository,
    ) {
    }

    public function refresh(ChannelInterface $channel): void
    {
        $urlSource = $channel->getUrlSource();
        if (!$urlSource instanceof ApiUrlSourceInterface) {
            throw CacheException::urlSourceNotSupported($channel->getTvgName());
        }

        $this->repository->insert($channel, $this->getStreamUrl($urlSource));
    }

    private function getStreamUrl(ApiUrlSourceInterface $urlSource): string
    {
        $request = $this->requestFactory->create($urlSource->getApiUrl());
        $request->useProxy($urlSource->isUsingProxy());
        return $this->jsonDecoder->decode($request->getResponse(), $urlSource->getJsonPath());
    }
}
