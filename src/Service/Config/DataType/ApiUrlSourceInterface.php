<?php

namespace App\Service\Config\DataType;

interface ApiUrlSourceInterface extends UrlSourceInterface
{
    public function getApiUrl(): string;
    public function getJsonPath(): string;
    public function isUsingProxy(): bool;
}
