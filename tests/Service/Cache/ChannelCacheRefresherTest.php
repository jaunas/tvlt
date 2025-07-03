<?php

namespace App\Tests\Service\Cache;

use App\Exception\CacheException;
use App\Service\Cache\CacheRepositoryInterface;
use App\Service\Cache\ChannelCacheRefresher;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;
use App\Service\Config\DataType\UrlSourceInterface;
use App\Service\HttpClient\RequestFactoryInterface;
use App\Service\HttpClient\RequestInterface;
use App\Service\JsonDecoderInterface;
use PHPUnit\Framework\TestCase;

class ChannelCacheRefresherTest extends TestCase
{
    public function testRefreshStaticUrlSource(): void
    {
        $channel = $this->createChannelStub($this->createStub(StaticUrlSourceInterface::class));
        $cacheManager = $this->getSut();

        $this->expectExceptionObject(CacheException::urlSourceNotSupported($channel->getTvgName()));
        $cacheManager->refresh($channel);
    }

    /**
     * @dataProvider useProxyProvider
     */
    public function testRefresh(bool $useProxy): void
    {
        $json = json_encode([uniqid()]);
        $streamUrl = sprintf('http://localhost/%s.json', uniqid());

        $apiUrlSource = $this->createApiUrlStub($useProxy);
        $channelStub = $this->createChannelStub($apiUrlSource);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())->method('useProxy')->with($useProxy);
        $requestMock->expects($this->once())->method('getResponse')->willReturn($json);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock->method('create')->with($apiUrlSource->getApiUrl())->willReturn($requestMock);

        $jsonDecoderMock = $this->createMock(JsonDecoderInterface::class);
        $jsonDecoderMock->expects($this->once())
            ->method('decode')->with($json, $apiUrlSource->getJsonPath())->willReturn($streamUrl);

        $repositoryMock = $this->createMock(CacheRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('insert')->with($channelStub, $streamUrl);

        $cacheManager = $this->getSut(
            requestFactory: $requestFactoryMock,
            jsonDecoder: $jsonDecoderMock,
            repository: $repositoryMock
        );
        $cacheManager->refresh($channelStub);
    }

    public function useProxyProvider(): array
    {
        return [
            ['useProxy' => false],
            ['useProxy' => true],
        ];
    }

    private function getSut(
        ?RequestFactoryInterface $requestFactory = null,
        ?JsonDecoderInterface $jsonDecoder = null,
        ?CacheRepositoryInterface $repository = null,
    ): ChannelCacheRefresher {
        return new ChannelCacheRefresher(
            requestFactory: $requestFactory ?? $this->createStub(RequestFactoryInterface::class),
            jsonDecoder: $jsonDecoder ?? $this->createStub(JsonDecoderInterface::class),
            repository: $repository ?? $this->createStub(CacheRepositoryInterface::class),
        );
    }

    private function createChannelStub(UrlSourceInterface $urlSource): ChannelInterface
    {
        return $this->createConfiguredMock(ChannelInterface::class, [
            'getTvgId' => uniqid(),
            'getTvgName' => uniqid(),
            'getUrlSource' => $urlSource,
        ]);
    }

    private function createApiUrlStub(bool $isUsingProxy = false): ApiUrlSourceInterface
    {
        return $this->createConfiguredMock(ApiUrlSourceInterface::class, [
            'getApiUrl' => sprintf('http://localhost/%s.json', uniqid()),
            'getJsonPath' => sprintf('$.%s.jsonPath', uniqid()),
            'isUsingProxy' => $isUsingProxy,
        ]);
    }
}
