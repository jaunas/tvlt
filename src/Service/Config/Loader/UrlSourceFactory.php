<?php

namespace App\Service\Config\Loader;

use App\Entity\ApiUrlSource;
use App\Entity\StaticUrlSource;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;

class UrlSourceFactory
{
    public function createStatic(string $streamUrl): StaticUrlSourceInterface
    {
        return new StaticUrlSource($streamUrl);
    }

    public function createApi(string $apiUrl, string $jsonPath, bool $useProxy = false): ApiUrlSourceInterface
    {
        return new ApiUrlSource($apiUrl, $jsonPath, $useProxy);
    }
}
