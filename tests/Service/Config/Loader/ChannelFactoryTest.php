<?php

namespace App\Tests\Service\Config\Loader;

use App\Service\Config\DataType\UrlSourceInterface;
use App\Service\Config\Loader\ChannelFactory;
use PHPUnit\Framework\TestCase;

class ChannelFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $tvgName = uniqid();
        $tvgId = uniqid();
        $urlSourceStub = $this->createStub(UrlSourceInterface::class);

        $factory = new ChannelFactory();
        $channel = $factory->create($tvgName, $tvgId, $urlSourceStub);

        $this->assertSame($tvgName, $channel->getTvgName());
        $this->assertSame($tvgId, $channel->getTvgId());
        $this->assertSame($urlSourceStub, $channel->getUrlSource());
    }
}
