<?php

namespace App\Entity;

use App\Repository\CacheRepository;
use App\Service\Cache\CacheInterface;
use App\Service\Config\DataType\ChannelInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheRepository::class)]
class Cache implements CacheInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cache')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChannelInterface $channel;

    #[ORM\Column(length: 2083)]
    private ?string $streamUrl;

    public function __construct(ChannelInterface $channel, ?string $streamUrl = null)
    {
        $this->channel = $channel;
        $this->streamUrl = $streamUrl;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getStreamUrl(): string
    {
        return $this->streamUrl;
    }

    public function setStreamUrl(string $streamUrl): void
    {
        $this->streamUrl = $streamUrl;
    }
}
