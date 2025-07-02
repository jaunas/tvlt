<?php

namespace App\Exception;

use Exception;

class FileException extends Exception
{

    public static function failedToReadYamlFile(): self
    {
        return new self('Failed to read YAML file.');
    }
}