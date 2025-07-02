<?php

namespace App\Repository;

use App\Entity\Config;
use App\Service\Config\ConfigRepositoryInterface;
use App\Service\Config\DataType\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Config>
 */
class ConfigRepository extends ServiceEntityRepository implements ConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    public function saveConfig(ConfigInterface $config): void
    {
        $manager = $this->getEntityManager();

        foreach ($this->findAll() as $existingConfig) {
            $manager->remove($existingConfig);
        }

        $manager->persist($config);
        $manager->flush();
    }

    public function loadConfig(): ConfigInterface
    {
        return $this->findOneBy([]);
    }
}
