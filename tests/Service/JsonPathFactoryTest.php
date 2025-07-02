<?php

namespace App\Tests\Service;

use App\Service\JsonPathFactory;
use PHPUnit\Framework\TestCase;

class JsonPathFactoryTest extends TestCase
{
    public function testCreateJsonPath(): void
    {
        $data = [uniqid() => [uniqid() => uniqid()]];

        $factory = new JsonPathFactory();
        $this->assertEquals($data, $factory->createWith(json_encode($data))->getData());
    }
}
