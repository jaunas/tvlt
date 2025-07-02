<?php

namespace App\Tests\Service;

use App\Service\JsonDecoder;
use App\Service\JsonPathFactory;
use Flow\JSONPath\JSONPath;
use PHPUnit\Framework\TestCase;

class JsonDecoderTest extends TestCase
{
    public function testDecode(): void
    {
        $json = uniqid();
        $jsonPath = uniqid();
        $value = uniqid();

        $jsonPathNestedMock = $this->createMock(JsonPath::class);
        $jsonPathNestedMock->expects($this->once())->method('first')->willReturn($value);

        $jsonPathMock = $this->createStub(JSONPath::class);
        $jsonPathMock->expects($this->once())->method('find')->with($jsonPath)->willReturn($jsonPathNestedMock);

        $jsonPathFactoryMock = $this->createMock(JsonPathFactory::class);
        $jsonPathFactoryMock->expects($this->once())->method('createWith')->with($json)->willReturn($jsonPathMock);

        $decoder = new JsonDecoder($jsonPathFactoryMock);
        $this->assertEquals($value, $decoder->decode($json, $jsonPath));
    }
}
