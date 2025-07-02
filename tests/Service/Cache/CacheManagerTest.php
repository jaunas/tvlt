<?php

namespace App\Tests\Service\Cache;

use App\Service\Cache\CacheManager;
use App\Service\Cache\ChannelCacheRefresher;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\ConfigInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    /**
     * @dataProvider notCachableChannelsProvider
     * @param ChannelInterface[] $channels
     */
    public function testDoNothingForNotCachableChannels(array $channels): void
    {
        $configStub = $this->createConfiguredMock(ConfigInterface::class, [
            'getChannels' => $channels,
        ]);

        $channelCacheManagerMock = $this->createMock(ChannelCacheRefresher::class);
        $channelCacheManagerMock->expects($this->never())->method('refresh');

        $cacheManager = new CacheManager($channelCacheManagerMock, $configStub);
        $cacheManager->cache();
    }

    /**
     * @return ChannelInterface[]
     */
    public function notCachableChannelsProvider(): array
    {
        return [
            [
                'channels' => [],
            ],
            [
                'channels' => [
                    $this->createConfiguredMock(ChannelInterface::class, [
                        'getUrlSource' => $this->createStub(StaticUrlSourceInterface::class),
                    ])
                ],
            ],
        ];
    }

    public function testCache(): void
    {
        $channelStub = $this->createConfiguredMock(ChannelInterface::class, [
            'getUrlSource' => $this->createStub(ApiUrlSourceInterface::class),
        ]);
        $configStub = $this->createConfiguredMock(ConfigInterface::class, [
            'getChannels' => [$channelStub],
        ]);

        $channelCacheManagerMock = $this->createMock(ChannelCacheRefresher::class);
        $channelCacheManagerMock->expects($this->once())->method('refresh')->with($channelStub);

        $cacheManager = new CacheManager($channelCacheManagerMock, $configStub);
        $cacheManager->cache();
    }
}
