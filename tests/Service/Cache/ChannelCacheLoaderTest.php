<?php

namespace App\Tests\Service\Cache;

use App\Exception\CacheException;
use App\Service\Cache\CacheInterface;
use App\Service\Cache\CacheRepositoryInterface;
use App\Service\Cache\ChannelCacheLoader;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;
use App\Service\Config\DataType\UrlSourceInterface;
use PHPUnit\Framework\TestCase;

class ChannelCacheLoaderTest extends TestCase
{
    public function testLoadForStaticUrlSourceThrowsException(): void
    {
        $channel = $this->createChannelStub($this->createStub(StaticUrlSourceInterface::class));

        $cacheManager = $this->getSut();
        $this->expectExceptionObject(CacheException::urlSourceNotSupported($channel->getTvgName()));
        $cacheManager->load($channel);
    }

    public function testLoadFromEmptyCacheTrowsException(): void
    {
        $channel = $this->createChannelStub($this->createStub(ApiUrlSourceInterface::class));

        $repositoryMock = $this->createMock(CacheRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('findByChannel')->with($channel)->willReturn(null);

        $cacheManager = $this->getSut(repository: $repositoryMock);

        $this->expectExceptionObject(CacheException::empty($channel->getTvgName()));
        $cacheManager->load($channel);
    }

    public function testLoadFromCache(): void
    {
        $channel = $this->createChannelStub($this->createStub(ApiUrlSourceInterface::class));
        $cacheStub = $this->createStub(CacheInterface::class);

        $repositoryMock = $this->createMock(CacheRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('findByChannel')->with($channel)->willReturn($cacheStub);

        $cacheManager = $this->getSut(repository: $repositoryMock);

        $cache = $cacheManager->load($channel);
        $this->assertInstanceOf(CacheInterface::class, $cache);
    }

    private function getSut(
        ?CacheRepositoryInterface $repository = null,
    ): ChannelCacheLoader {
        return new ChannelCacheLoader(
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
}
