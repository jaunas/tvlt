<?php

namespace App\Tests\Repository;

use App\Entity\Config;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RepositoryTest extends KernelTestCase
{
    protected ?EntityManager $entityManager;

    /**
     * @var ?EntityRepository<Config>
     */
    protected ?EntityRepository $repository;

    abstract protected function getEntityClassName(): string;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository($this->getEntityClassName());
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
