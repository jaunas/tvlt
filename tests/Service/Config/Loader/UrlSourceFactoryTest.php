<?php

namespace App\Tests\Service\Config\Loader;

use App\Service\Config\Loader\UrlSourceFactory;
use PHPUnit\Framework\TestCase;

class UrlSourceFactoryTest extends TestCase
{
    public function testCreateStatic(): void
    {
        $streamUrl = sprintf('http://localhost/%s.m3u8', uniqid());

        $factory = new UrlSourceFactory();
        $urlSource = $factory->createStatic($streamUrl);

        $this->assertSame($streamUrl, $urlSource->getStreamUrl());
    }

    public function testCreateApi(): void
    {
        $apiUrl = sprintf('http://localhost/%s.json', uniqid());
        $jsonPath = sprintf('$.%s.url', uniqid());
        $useProxy = (bool)rand(0, 1);

        $factory = new UrlSourceFactory();
        $urlSource = $factory->createApi($apiUrl, $jsonPath, $useProxy);

        $this->assertSame($apiUrl, $urlSource->getApiUrl());
        $this->assertSame($jsonPath, $urlSource->getJsonPath());
        $this->assertSame($useProxy, $urlSource->isUsingProxy());
    }
}
