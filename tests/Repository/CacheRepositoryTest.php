<?php

namespace App\Tests\Repository;

use App\Entity\ApiUrlSource;
use App\Entity\Cache;
use App\Entity\Channel;
use Doctrine\ORM\EntityRepository;

class CacheRepositoryTest extends RepositoryTest
{
    /**
     * @var ?EntityRepository<Cache>
     */
    protected ?EntityRepository $repository;

    protected function getEntityClassName(): string
    {
        return Cache::class;
    }

    public function testLoadFromEmptyCache(): void
    {
        $channel = $this->prepareChannel();
        $cache = $this->repository->findByChannel($channel);
        $this->assertNull($cache);
    }

    public function testInsertAndLoad(): void
    {
        $channel = $this->prepareChannel();
        $cache = new Cache($channel, $streamUrl = sprintf('http://localhost/%s.m3u8', uniqid()));

        $this->repository->insert($channel, $streamUrl);
        $this->entityManager->clear();

        $loadedCache = $this->repository->findByChannel($channel);
        $this->assertEquals($cache->getStreamUrl(), $loadedCache->getStreamUrl());

        $loadedChannel = $loadedCache->getChannel();
        $this->assertEquals($channel->getTvgName(), $loadedChannel->getTvgName());
        $this->assertEquals($channel->getTvgId(), $loadedChannel->getTvgId());
        $this->assertEquals($channel->getUrlSource(), $loadedChannel->getUrlSource());
    }

    public function testReplace(): void
    {
        $channel = $this->prepareChannel();

        $firstStreamUrl = sprintf('http://localhost/%s.m3u8', uniqid());
        $this->repository->insert($channel, $firstStreamUrl);

        $secondStreamUrl = sprintf('http://localhost/%s.m3u8', uniqid());
        $this->repository->insert($channel, $secondStreamUrl);

        $this->entityManager->clear();
        $loadedCache = $this->repository->findByChannel($channel);
        $this->assertEquals($secondStreamUrl, $loadedCache->getStreamUrl());
    }

    private function prepareChannel(): Channel
    {
        $apiUrlSource = new ApiUrlSource(
            sprintf('http://localhost/%s.json', uniqid()),
            sprintf('$.%s.url', uniqid()),
        );
        $channel = new Channel(uniqid(), uniqid(), $apiUrlSource);

        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return $channel;
    }
}
