<?php

namespace App\Service;

interface JsonDecoderInterface
{
    public function decode(string $json, string $jsonPath): string;
}
