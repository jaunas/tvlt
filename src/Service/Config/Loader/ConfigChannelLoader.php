<?php

namespace App\Service\Config\Loader;

use App\Exception\Config\BadChannelException;
use App\Exception\Config\BadUrlSourceException;
use App\Service\Config\DataType\ChannelInterface;

class ConfigChannelLoader
{
    public function __construct(
        private UrlSourceLoader $urlSourceLoader,
        private ChannelFactory  $channelFactory,
    ) {
    }

    public function load(mixed $channel): ChannelInterface
    {
        if (!is_array($channel)) {
            throw BadChannelException::notArray($channel);
        }

        $missingFields = [];
        if (!isset($channel['name'])) {
            $missingFields[] = 'name';
        }
        if (!isset($channel['id'])) {
            $missingFields[] = 'id';
        }
        if (!isset($channel['urlSource'])) {
            $missingFields[] = 'urlSource';
        }

        if (!empty($missingFields)) {
            throw BadChannelException::missingFields($channel, $missingFields);
        }

        try {
            $urlSource = $this->urlSourceLoader->load($channel['urlSource']);
        } catch (BadUrlSourceException $exception) {
            throw BadChannelException::badUrlSource($channel['name'], $exception);
        }

        return $this->channelFactory->create($channel['name'], $channel['id'], $urlSource);
    }
}
