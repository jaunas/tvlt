<?php

namespace App\Service;

class JsonDecoder implements JsonDecoderInterface
{
    public function __construct(private JsonPathFactory $jsonPathFactory)
    {
    }

    public function decode(string $json, string $jsonPath): string
    {
        return $this->jsonPathFactory->createWith($json)->find($jsonPath)->first();
    }
}
