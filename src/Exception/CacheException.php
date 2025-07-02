<?php

namespace App\Exception;

use Exception;

class CacheException extends Exception
{
    public static function empty(string $channelName): self
    {
        return new self(sprintf('Cache is empty for channel %s.', $channelName));
    }

    public static function urlSourceNotSupported(string $channelName): self
    {
        return new self(sprintf('Cache is not supported for static url source for channel %s.', $channelName));
    }
}
