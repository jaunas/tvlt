<?php

namespace App\Service\HttpClient;

use App\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;

class Request implements RequestInterface
{
    public const MAX_ATTEMPTS = 20;
    public const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64; rv:136.0) Gecko/20100101 Firefox/136.0';

    private bool $useProxy = false;
    private ?HandlerStack $handlerStack = null;

    public function __construct(private string $url, private ?string $proxyUrl = null)
    {
    }

    public function getResponse(): string
    {
        $client = new Client($this->getOptions());

        $attempts = 0;
        while ($attempts < self::MAX_ATTEMPTS) {
            try {
                $response = $client->request('GET', $this->url);
                return $response->getBody();
            } catch (TransferException) {}

            $attempts++;
        }

        throw RequestException::maxAttemptsExceeded();
    }

    private function getOptions(): array
    {
        $options = [
            'headers' => [
                'User-Agent' => self::USER_AGENT,
            ]
        ];

        if ($this->useProxy) {
            if ($this->proxyUrl === null) {
                throw RequestException::noProxyConfigured();
            }
            $options['proxy'] = $this->proxyUrl;
        }

        if ($this->handlerStack) {
            $options['handler'] = $this->handlerStack;
        }

        return $options;
    }

    public function useProxy(bool $useProxy): void
    {
        $this->useProxy = $useProxy;
    }

    /**
     * Set custom handlerStack for testing purposes
     */
    public function setHandlerStack(HandlerStack $handlerStack): void
    {
        $this->handlerStack = $handlerStack;
    }
}
