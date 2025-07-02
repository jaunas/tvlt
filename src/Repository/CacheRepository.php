<?php

namespace App\Repository;

use App\Entity\Cache;
use App\Service\Cache\CacheInterface;
use App\Service\Cache\CacheRepositoryInterface;
use App\Service\Config\DataType\ChannelInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cache>
 */
class CacheRepository extends ServiceEntityRepository implements CacheRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cache::class);
    }

    public function findByChannel(ChannelInterface $channel): ?CacheInterface
    {
        return parent::findOneByChannel($channel);
    }

    public function insert(ChannelInterface $channel, string $streamUrl): void
    {
        $cache = $this->findByChannel($channel);
        if (null === $cache) {
            $cache = new Cache($channel, $streamUrl);
        } else {
            $cache->setStreamUrl($streamUrl);
        }

        $this->getEntityManager()->persist($cache);
        $this->getEntityManager()->flush();
    }
}
