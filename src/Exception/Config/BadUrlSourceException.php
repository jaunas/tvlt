<?php

namespace App\Exception\Config;

class BadUrlSourceException extends ConfigException
{
    public static function notArray(): self
    {
        return new self('Url source configuration is not an array.');
    }

    public static function missingType(): self
    {
        return new self('Url source configuration is missing type.');
    }

    public static function wrongType(): self
    {
        return new self("Url source configuration has invalid type. Possible choices are 'static' or 'api'.");
    }

    public static function missingFields(array $missingFields): self
    {
        return new self(
            sprintf('Url source configuration is missing required fields: %s.', implode(', ', $missingFields))
        );
    }
}
