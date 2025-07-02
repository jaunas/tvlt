<?php

namespace App\Service\Config\Loader;

use App\Exception\Config\BadUrlSourceException;
use App\Service\Config\DataType\ApiUrlSourceInterface;
use App\Service\Config\DataType\StaticUrlSourceInterface;
use App\Service\Config\DataType\UrlSourceInterface;

class UrlSourceLoader
{
    public function __construct(private UrlSourceFactory $urlSourceFactory)
    {
    }

    public function load(mixed $urlSource): UrlSourceInterface
    {
        if (!is_array($urlSource)) {
            throw BadUrlSourceException::notArray();
        }

        if (!isset($urlSource['type'])) {
            throw BadUrlSourceException::missingType();
        }

        return match ($urlSource['type']) {
            'static' => $this->loadStatic($urlSource),
            'api' => $this->loadApi($urlSource),
            default => throw BadUrlSourceException::wrongType(),
        };
    }

    private function loadStatic(array $urlSource): StaticUrlSourceInterface
    {
        if (!isset($urlSource['streamUrl'])) {
            throw BadUrlSourceException::missingFields(['streamUrl']);
        }

        return $this->urlSourceFactory->createStatic($urlSource['streamUrl']);
    }

    private function loadApi(array $urlSource): ApiUrlSourceInterface
    {
        $requiredFields = ['apiUrl', 'jsonPath'];
        $missingFields = array_filter($requiredFields, fn($field) => !isset($urlSource[$field]));

        if (!empty($missingFields)) {
            throw BadUrlSourceException::missingFields($missingFields);
        }

        if (isset($urlSource['useProxy'])) {
            return $this->urlSourceFactory->createApi($urlSource['apiUrl'], $urlSource['jsonPath'], $urlSource['useProxy']);
        } else {
            return $this->urlSourceFactory->createApi($urlSource['apiUrl'], $urlSource['jsonPath']);
        }
    }
}
