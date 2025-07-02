<?php

namespace App\Tests\Service\HttpClient;

use App\Exception\RequestException;
use App\Service\HttpClient\RequestFactory;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
    public function testCreateRequestUsingProxyWithoutProxyConfiguredThrowsException(): void
    {
        $factory = new RequestFactory();
        $request = $factory->create(sprintf('http://localhost/%s', uniqid()));
        $request->useProxy(true);

        $this->expectExceptionObject(RequestException::noProxyConfigured());
        $request->getResponse();
    }

    /**
     * @dataProvider proxyProvider
     */
    public function testCreateRequest(?string $proxyUrl): void
    {
        $url = sprintf('http://localhost/%s', uniqid());

        $factory = new RequestFactory($proxyUrl);
        $request = $factory->create($url);
        $request->useProxy((bool)$proxyUrl);

        $handlerStack = HandlerStack::create(new MockHandler([new Response()]));

        $container = [];
        $handlerStack->push(Middleware::history($container));
        $request->setHandlerStack($handlerStack);

        $request->getResponse();

        $this->assertCount(1, $container);
        $attempt = $container[0];
        $this->assertEquals($url, $attempt['request']->getUri());
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
}
