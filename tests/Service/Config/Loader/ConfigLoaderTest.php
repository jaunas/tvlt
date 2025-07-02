<?php

namespace App\Tests\Service\Config\Loader;

use App\Exception\Config\BadConfigException;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\ConfigInterface;
use App\Service\Config\Loader\ConfigChannelLoader;
use App\Service\Config\Loader\ConfigFactory;
use App\Service\Config\Loader\ConfigLoader;
use PHPUnit\Framework\TestCase;

class ConfigLoaderTest extends TestCase
{
    /**
     * @dataProvider emptyConfigProvider
     */
    public function testLoadFromEmptyThrowsException(array $configArray): void
    {
        $configLoader = $this->getSut();
        $this->expectExceptionObject(BadConfigException::noChannelsArray());
        $configLoader->load($configArray);
    }

    public function emptyConfigProvider(): iterable
    {
        yield ['configArray' => []];
        yield ['configArray' => [
            'channels' => uniqid(),
        ]];
    }

    public function testLoadFromConfigArrayWrongChannelsThrowsException(): void
    {
        $configLoader = $this->getSut();
        $this->expectExceptionObject(BadConfigException::noChannels());
        $configLoader->load(['channels' => []]);
    }

    public function testLoadFromConfigArray(): void
    {
        $configStub = $this->createStub(ConfigInterface::class);

        $channelsData = [uniqid(), uniqid()];
        $channelStubs = [
            $this->createStub(ChannelInterface::class),
            $this->createStub(ChannelInterface::class),
        ];

        $channelLoaderMock = $this->createMock(ConfigChannelLoader::class);
        $channelLoaderMock->expects($this->exactly(2))
            ->method('load')->willReturnMap([
                [$channelsData[0], $channelStubs[0]],
                [$channelsData[1], $channelStubs[1]],
            ]);

        $configFactoryMock = $this->createMock(ConfigFactory::class);
        $configFactoryMock->expects($this->once())->method('create')->with($channelStubs, null)->willReturn($configStub);

        $configLoader = $this->getSut($channelLoaderMock, $configFactoryMock);
        $this->assertSame($configStub, $configLoader->load(['channels' => $channelsData]));
    }

    public function testLoadFromConfigWithProxy(): void
    {
        $channelStubs = [
            uniqid() => $this->createStub(ChannelInterface::class),
            uniqid() => $this->createStub(ChannelInterface::class),
        ];
        $proxyUrl = sprintf('socks5://localhost/%s', uniqid());

        $configStub = $this->createStub(ConfigInterface::class);

        $channelLoaderStub = $this->createStub(ConfigChannelLoader::class);
        $channelLoaderStub->method('load')->willReturnMap(array_map(
            fn($channelConfig, $channelStub) => [$channelConfig, $channelStub],
            array_keys($channelStubs),
            $channelStubs,
        ));

        $configFactoryMock = $this->createMock(ConfigFactory::class);
        $configFactoryMock->expects($this->once())->method('create')
            ->with(array_values($channelStubs), $proxyUrl)->willReturn($configStub);

        $configLoader = $this->getSut(channelLoader: $channelLoaderStub, configFactory: $configFactoryMock);
        $config = $configLoader->load(['channels' => array_keys($channelStubs), 'proxy' => $proxyUrl]);

        $this->assertSame($configStub, $config);
    }

    private function getSut(
        ConfigChannelLoader $channelLoader = null,
        ConfigFactory $configFactory = null,
    ): ConfigLoader {
        return new ConfigLoader(
            channelLoader: $channelLoader ?? $this->createStub(ConfigChannelLoader::class),
            configFactory: $configFactory ?? $this->createStub(ConfigFactory::class),
        );
    }
}
