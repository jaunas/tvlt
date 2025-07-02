<?php

namespace App\Tests\Service\Config\Loader;

use App\Exception\Config\BadUrlSourceException;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;
use App\Service\Config\Loader\UrlSourceFactory;
use App\Service\Config\Loader\UrlSourceLoader;
use PHPUnit\Framework\TestCase;

class UrlSourceLoaderTest extends TestCase
{
    /**
     * @dataProvider malformedConfigProvider
     */
    public function testLoadMalformedConfigThrowsException(
        mixed $urlSourceData,
        string $expectedExceptionMethod
    ): void {
        $loader = $this->getSut();
        $this->expectExceptionObject(BadUrlSourceException::$expectedExceptionMethod());
        $loader->load($urlSourceData);
    }

    public function malformedConfigProvider(): iterable
    {
        yield [
            'urlSourceData' => uniqid(),
            'expectedExceptionMethod' => 'notArray',
        ];

        yield [
            'urlSourceData' => [],
            'expectedExceptionMethod' => 'missingType',
        ];

        yield [
            'urlSourceData' => ['type' => uniqid()],
            'expectedExceptionMethod' => 'wrongType',
        ];
    }

    public function testLoadMissingStaticFieldsThrowsException(): void
    {
        $loader = $this->getSut();
        $this->expectExceptionObject(BadUrlSourceException::missingFields(['streamUrl']));
        $loader->load(['type' => 'static']);
    }

    public function testLoadStaticUrlSource(): void
    {
        $streamUrl = sprintf('http://localhost/%s.m3u8', uniqid());

        $urlSourceStub = $this->createStub(StaticUrlSourceInterface::class);

        $factoryMock = $this->createMock(UrlSourceFactory::class);
        $factoryMock->expects($this->once())->method('createStatic')->with($streamUrl)->willReturn($urlSourceStub);

        $loader = $this->getSut(urlSourceFactory: $factoryMock);
        $urlSource = $loader->load(['type' => 'static', 'streamUrl' => $streamUrl]);
        $this->assertEquals($urlSourceStub, $urlSource);
    }

    /**
     * @dataProvider missingApiFieldsProvider
     */
    public function testLoadMissingApiFieldsThrowsException(
        array $urlSourceConfig,
        array $missingFields
    ): void {
        $loader = $this->getSut();
        $this->expectExceptionObject(BadUrlSourceException::missingFields($missingFields));
        $loader->load(['type' => 'api'] + $urlSourceConfig);
    }

    public function missingApiFieldsProvider(): iterable
    {
        yield [
            'urlSourceConfig' => [],
            'missingFields' => ['apiUrl', 'jsonPath'],
        ];

        yield [
            'urlSourceConfig' => ['apiUrl' => sprintf('http://localhost/%s.json', uniqid())],
            'missingFields' => ['jsonPath'],
        ];

        yield [
            'urlSourceConfig' => ['jsonPath' => sprintf('$.%s.url', uniqid())],
            'missingFields' => ['apiUrl'],
        ];
    }

    public function testLoadApiUrlSource(): void
    {
        $apiUrl = sprintf('http://localhost/%s.json', uniqid());
        $jsonPath = sprintf('$.%s.url', uniqid());

        $urlSourceStub = $this->createStub(ApiUrlSourceInterface::class);

        $factoryMock = $this->createMock(UrlSourceFactory::class);
        $factoryMock->expects($this->once())->method('createApi')->with($apiUrl, $jsonPath)->willReturn($urlSourceStub);

        $loader = $this->getSut(urlSourceFactory: $factoryMock);
        $urlSource = $loader->load(['type' => 'api', 'apiUrl' => $apiUrl, 'jsonPath' => $jsonPath]);

        $this->assertSame($urlSourceStub, $urlSource);
    }

    public function testLoadApiUrlSourceWithProxy(): void
    {
        $apiUrl = sprintf('http://localhost/%s.json', uniqid());
        $jsonPath = sprintf('$.%s.url', uniqid());
        $useProxy = (bool)rand(0, 1);

        $urlSourceStub = $this->createStub(ApiUrlSourceInterface::class);

        $factoryMock = $this->createMock(UrlSourceFactory::class);
        $factoryMock->expects($this->once())->method('createApi')
            ->with($apiUrl, $jsonPath, $useProxy)->willReturn($urlSourceStub);

        $loader = $this->getSut(urlSourceFactory: $factoryMock);
        $urlSource = $loader->load([
            'type' => 'api',
            'apiUrl' => $apiUrl,
            'jsonPath' => $jsonPath,
            'useProxy' => $useProxy
        ]);

        $this->assertSame($urlSourceStub, $urlSource);
    }

    private function getSut(UrlSourceFactory $urlSourceFactory = null): UrlSourceLoader
    {
        return new UrlSourceLoader(
            urlSourceFactory: $urlSourceFactory ?? $this->createStub(UrlSourceFactory::class),
        );
    }
}
