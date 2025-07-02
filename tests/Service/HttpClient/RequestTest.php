<?php

namespace App\Tests\Service\HttpClient;

use App\Exception\RequestException;
use App\Service\HttpClient\Request;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetResponseUsingProxyWithoutProxyConfiguredThrowsException(): void
    {
        $request = new Request(sprintf('http://localhost/%s', uniqid()));
        $request->useProxy(true);

        $this->expectExceptionObject(RequestException::noProxyConfigured());
        $request->getResponse();
    }

    /**
     * @dataProvider proxyProvider
     */
    public function testGetResponse(?string $proxyUrl): void
    {
        $url = sprintf('http://localhost/%s', uniqid());

        $request = new Request($url, $proxyUrl);
        $request->useProxy((bool)$proxyUrl);

        $handlerStack = HandlerStack::create(new MockHandler([new Response()]));

        $container = [];
        $handlerStack->push(Middleware::history($container));
        $request->setHandlerStack($handlerStack);

        $request->getResponse();

        $this->assertCount(1, $container);
        $attempt = $container[0];
        $sentRequest = $attempt['request'];

        $this->assertEquals($url, $sentRequest->getUri());
        $this->assertEquals([Request::USER_AGENT], $sentRequest->getHeader('User-Agent'));

        if ($proxyUrl) {
            $this->assertEquals($proxyUrl, $attempt['options']['proxy']);
        }
    }

    public function proxyProvider(): array
    {
        return [
            ['proxyUrl' => null],
            ['proxyUrl' => sprintf('socks5://localhost/%s:12345', uniqid())],
        ];
    }

    /**
     * @dataProvider attemptsProvider
     */
    public function testGetResponseMultipleAttempts(int $attempts): void
    {
        $url = sprintf('http://localhost/%s', uniqid());

        $request = new Request($url);
        $response = uniqid();

        $queue = [];
        for ($i = 0; $i < $attempts; $i++) {
            $queue[] = new TransferException();
        }
        $queue[] = new Response(body: $response);

        $request->setHandlerStack(HandlerStack::create(new MockHandler($queue)));

        $this->assertSame($response, $request->getResponse());
    }

    public function attemptsProvider(): array
    {
        return [
            ['attempts' => 1],
            ['attempts' => rand(1, Request::MAX_ATTEMPTS-1)],
            ['attempts' => rand(1, Request::MAX_ATTEMPTS-1)],
            ['attempts' => Request::MAX_ATTEMPTS-1],
        ];
    }

    public function testGetResponseExceedingMaxAttemptsThrowsException(): void
    {
        $url = sprintf('http://localhost/%s', uniqid());

        $request = new Request($url);

        $queue = [];
        for ($i = 0; $i < Request::MAX_ATTEMPTS; $i++) {
            $queue[] = new TransferException();
        }

        $request->setHandlerStack(HandlerStack::create(new MockHandler($queue)));

        $this->expectExceptionObject(RequestException::maxAttemptsExceeded());
        $request->getResponse();
    }
}
