<?php

namespace App\Service\HttpClient;

class RequestFactory implements RequestFactoryInterface
{
    public function __construct(private ?string $proxyUrl = null)
    {
    }

    public function create(string $url): RequestInterface
    {
        return new Request($url, $this->proxyUrl);
    }
}