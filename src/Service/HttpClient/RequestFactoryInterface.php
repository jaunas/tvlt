<?php

namespace App\Service\HttpClient;

interface RequestFactoryInterface
{
    public function create(string $url): RequestInterface;
}
