<?php

namespace App\Entity;

use App\Service\Config\DataType\ChannelInterface;
use App\Service\Config\DataType\ConfigInterface;
use App\Service\Config\DataType\UrlSourceInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Channel implements ChannelInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tvgName;

    #[ORM\Column(length: 255)]
    private ?string $tvgId;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UrlSourceInterface $urlSource;

    #[ORM\ManyToOne(inversedBy: 'channels')]
    private ?ConfigInterface $config = null;

    public function __construct(string $tvgName, string $tvgId, UrlSourceInterface $urlSource)
    {
        $this->tvgName = $tvgName;
        $this->tvgId = $tvgId;
        $this->urlSource = $urlSource;
    }

    public function getTvgName(): string
    {
        return $this->tvgName;
    }

    public function getTvgId(): string
    {
        return $this->tvgId;
    }

    public function getUrlSource(): UrlSourceInterface
    {
        return $this->urlSource;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;

        return $this;
    }
}
