<?php

namespace App\Exception;

use Exception;

class RequestException extends Exception
{

    public static function noProxyConfigured(): self
    {
        return new self('Tried to request using proxy, but no proxy configuration provided.');
    }

    public static function maxAttemptsExceeded(): self
    {
        return new self('Maximum number of attempts exceeded.');
    }
}
