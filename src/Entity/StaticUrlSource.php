<?php

namespace App\Entity;

use App\Service\Config\DataType\StaticUrlSourceInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class StaticUrlSource extends UrlSource implements StaticUrlSourceInterface
{
    #[ORM\Column(length: 2083)]
    private ?string $streamUrl;

    public function __construct(string $streamUrl)
    {
        $this->streamUrl = $streamUrl;
    }

    public function getStreamUrl(): string
    {
        return $this->streamUrl;
    }
}
