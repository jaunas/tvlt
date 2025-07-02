<?php

namespace App\Service;

use Flow\JSONPath\JSONPath;

class JsonPathFactory
{

    public function createWith(string $json): JSONPath
    {
        return new JSONPath(json_decode($json, true));
    }
}