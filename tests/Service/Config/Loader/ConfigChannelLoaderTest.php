<?php

namespace App\Tests\Service\Config\Loader;

use App\Entity\Channel;
use App\Entity\UrlSource;
use App\Exception\Config\BadChannelException;
use App\Exception\Config\BadUrlSourceException;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\UrlSourceInterface;
use App\Service\Config\Loader\ChannelFactory;
use App\Service\Config\Loader\ConfigChannelLoader;
use App\Service\Config\Loader\UrlSourceLoader;
use PHPUnit\Framework\TestCase;

class ConfigChannelLoaderTest extends TestCase
{
    public function testLoadNotArrayThrowsException(): void
    {
        $channel = uniqid();
        $loader = $this->getSut();
        $this->expectExceptionObject(BadChannelException::notArray($channel));
        $loader->load($channel);
    }

    /**
     * @dataProvider missingFieldsConfigProvider
     */
    public function testLoadMissingFieldsThrowsException(
        array $channel,
        array $missingFields,
    ): void {
        $loader = $this->getSut();
        $this->expectExceptionObject(BadChannelException::missingFields($channel, $missingFields));
        $loader->load($channel);
    }

    public function missingFieldsConfigProvider(): iterable
    {
        yield [
            'channel' => [],
            'missingFields' => ['name', 'id', 'urlSource'],
        ];

        yield [
            'channel' => ['name' => uniqid()],
            'missingFields' => ['id', 'urlSource'],
        ];

        yield [
            'channel' => ['name' => uniqid(), 'id' => uniqid()],
            'missingFields' => ['urlSource'],
        ];
    }

    public function testHandleUrlSourceException(): void
    {
        $channelName = uniqid();

        $urlSourceData = uniqid();
        $urlSourceLoaderExceptionMessage = uniqid();
        $badUrlSourceException = new BadUrlSourceException($urlSourceLoaderExceptionMessage);

        $urlSourceLoaderMock = $this->createMock(UrlSourceLoader::class);
        $urlSourceLoaderMock->method('load')->with($urlSourceData)
            ->willThrowException($badUrlSourceException);

        $loader = $this->getSut(urlSourceLoader: $urlSourceLoaderMock);

        $this->expectExceptionObject(BadChannelException::badUrlSource($channelName, $badUrlSourceException));
        try {
            $loader->load(['name' => $channelName, 'id' => uniqid(), 'urlSource' => $urlSourceData]);
        } catch (BadChannelException $channelException) {
            $this->assertSame($badUrlSourceException, $channelException->getPrevious());
            throw $channelException;
        }
    }

    public function testLoad(): void
    {
        $channelData = [
            'name' => uniqid(),
            'id' => uniqid(),
            'urlSource' => uniqid(),
        ];
        $urlSource = $this->createStub(UrlSourceInterface::class);
        $channelStub = $this->createStub(ChannelInterface::class);

        $urlSourceLoaderMock = $this->createMock(UrlSourceLoader::class);
        $urlSourceLoaderMock->expects($this->once())
            ->method('load')->with($channelData['urlSource'])->willReturn($urlSource);

        $channelFactoryMock = $this->createMock(ChannelFactory::class);
        $channelFactoryMock->expects($this->once())->method('create')
            ->with($channelData['name'], $channelData['id'], $urlSource)->willReturn($channelStub);

        $configLoader = $this->getSut(urlSourceLoader: $urlSourceLoaderMock, channelFactory: $channelFactoryMock);
        $channel = $configLoader->load($channelData);

        $this->assertSame($channelStub, $channel);
    }

    private function getSut(
        UrlSourceLoader $urlSourceLoader = null,
        ChannelFactory $channelFactory = null
    ): ConfigChannelLoader {
        return new ConfigChannelLoader(
            urlSourceLoader: $urlSourceLoader ?? $this->createStub(UrlSourceLoader::class),
            channelFactory: $channelFactory ?? $this->createStub(ChannelFactory::class),
        );
    }
}
