<?php

namespace App\Exception;

use Exception;

class HttpClientException extends Exception
{

    public static function proxyNotConfigured(): self
    {
        return new self('Attempted to create http client with proxy, but it is not configured.');
    }
}
