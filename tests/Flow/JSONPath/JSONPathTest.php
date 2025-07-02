<?php

namespace App\Tests\Flow\JSONPath;

use Flow\JSONPath\JSONPath;
use PHPUnit\Framework\TestCase;

class JSONPathTest extends TestCase
{
    public function testJsonPath(): void
    {
        $json = sprintf('{"%s":{"%s":"%s"}}', $topLevel = uniqid(), $nestedLevel = uniqid(), $nestedValue = uniqid());
        $jsonPath = new JSONPath(json_decode($json, true));
        $this->assertEquals($nestedValue, $jsonPath->find(sprintf('$.%s.%s', $topLevel, $nestedLevel))->first());
    }
}
