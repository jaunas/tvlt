<?php

namespace App\Exception\Config;

class BadConfigException extends ConfigException
{
    public static function noChannelsArray(): self
    {
        return new self('Configuration does not contain channels array.');
    }

    public static function noChannels(): self
    {
        return new self('No channels found.');
    }
}
