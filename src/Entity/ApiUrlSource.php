<?php

namespace App\Entity;

use App\Service\Config\DataType\ApiUrlSourceInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ApiUrlSource extends UrlSource implements ApiUrlSourceInterface
{
    #[ORM\Column(length: 2083)]
    private ?string $apiUrl;

    #[ORM\Column(length: 255)]
    private ?string $jsonPath;

    #[ORM\Column]
    private ?bool $isUsingProxy;

    public function __construct(string $apiUrl, string $jsonPath, bool $useProxy = false)
    {
        $this->apiUrl = $apiUrl;
        $this->jsonPath = $jsonPath;
        $this->isUsingProxy = $useProxy;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getJsonPath(): string
    {
        return $this->jsonPath;
    }

    public function isUsingProxy(): bool
    {
        return $this->isUsingProxy;
    }
}
