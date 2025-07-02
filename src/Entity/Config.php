<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\ConfigInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config implements ConfigInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Channel>
     */
    #[ORM\OneToMany(targetEntity: ChannelInterface::class, mappedBy: 'config', cascade: ['persist', 'remove'])]
    private Collection $channels;

    #[ORM\Column(length: 2083, nullable: true)]
    private ?string $proxyUrl;

    /**
     * @param ChannelInterface[] $channels
     */
    public function __construct(array $channels, ?string $proxyUrl = null)
    {
        $this->channels = new ArrayCollection();
        foreach ($channels as $channel) {
            $this->addChannel($channel);
        }

        $this->proxyUrl = $proxyUrl;
    }

    /**
     * @return Collection<int, ChannelInterface>
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    private function addChannel(ChannelInterface $channel): void
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
            $channel->setConfig($this);
        }
    }

    public function getProxyUrl(): ?string
    {
        return $this->proxyUrl;
    }
}
