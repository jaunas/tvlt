<?php

namespace App\Tests\Service\Config\Loader;

use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\Loader\ConfigFactory;
use PHPUnit\Framework\TestCase;

class ConfigFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $channelStubs = [
            $this->createStub(ChannelInterface::class),
            $this->createStub(ChannelInterface::class),
        ];

        $proxyUrl = sprintf('socks5://localhost/%s:12345', uniqid());

        $configFactory = new ConfigFactory();
        $config = $configFactory->create($channelStubs, $proxyUrl);

        $this->assertEquals($channelStubs, $config->getChannels()->toArray());
        $this->assertSame($proxyUrl, $config->getProxyUrl());
    }
}
