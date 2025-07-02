<?php

namespace App\Tests\Repository;

use App\Entity\ApiUrlSource;
use App\Entity\Channel;
use App\Entity\Config;
use App\Entity\StaticUrlSource;
use App\Service\Config\DataType\UrlSourceInterface;
use Doctrine\ORM\EntityRepository;

class ConfigRepositoryTest extends RepositoryTest
{
    /**
     * @var ?EntityRepository<Config>
     */
    protected ?EntityRepository $repository;

    protected function getEntityClassName(): string
    {
        return Config::class;
    }

    public function testSaveConfig(): void
    {
        $channels = [];

        $staticChannel = $this->createChannel($this->createStaticUrlSource());
        $channels[] = $staticChannel;

        $apiChannel = $this->createChannel($this->createApiUrlSource());
        $channels[] = $apiChannel;

        $apiProxyUrlSource = $this->createApiUrlSource(true);
        $apiProxyChannel = $this->createChannel($apiProxyUrlSource);
        $channels[] = $apiProxyChannel;

        $config = new Config($channels, sprintf('socks5://localhost/%s:12345', uniqid()));

        $this->repository->saveConfig($config);
        $this->entityManager->clear();

        $loadedConfig = $this->repository->loadConfig();
        $this->assertEquals($config->getProxyUrl(), $loadedConfig->getProxyUrl());
        $this->assertEquals(
            [$staticChannel, $apiChannel, $apiProxyChannel],
            $loadedConfig->getChannels()->getValues(),
        );
    }

    private function createChannel(UrlSourceInterface $urlSource): Channel
    {
        return new Channel(uniqid(), uniqid(), $urlSource);
    }

    private function createStaticUrlSource(): StaticUrlSource
    {
        return new StaticUrlSource(sprintf('http://localhost/%s.m3u8', uniqid()));
    }

    private function createApiUrlSource(?bool $useProxy = null): ApiUrlSource
    {
        $apiUrl = sprintf('http://localhost/%s.json', uniqid());
        $jsonPath = sprintf('$.%s.url', uniqid());

        return match ($useProxy) {
            null => new ApiUrlSource($apiUrl, $jsonPath),
            default => new ApiUrlSource($apiUrl, $jsonPath, $useProxy),
        };
    }
}
