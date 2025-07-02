<?php

namespace App\Service\HttpClient;

use GuzzleHttp\HandlerStack;

interface RequestInterface
{
    public function getResponse(): string;
    public function useProxy(bool $useProxy): void;
    public function setHandlerStack(HandlerStack $handlerStack): void;
}
