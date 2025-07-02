<?php

namespace App\Service\Config\DataType;

interface StaticUrlSourceInterface extends UrlSourceInterface
{
    public function getStreamUrl(): string;
}
