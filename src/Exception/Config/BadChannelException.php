<?php

namespace App\Exception\Config;

class BadChannelException extends ConfigException
{
    public static function notArray(mixed $channel): self
    {
        return new self(sprintf('Channel entry is not an array. Channel data: %s', $channel));
    }

    public static function missingFields(array $channel, array $missingFields): self
    {
        return new self(sprintf(
            'Missing mandatory fields for channel: %s. Channel data: %s',
            implode(', ', $missingFields),
            print_r($channel, true),
        ));
    }

    public static function badUrlSource(string $channelName, BadUrlSourceException $exception): self
    {
        return new self(sprintf('Bad url source for channel %s.', $channelName), previous: $exception);
    }
}
