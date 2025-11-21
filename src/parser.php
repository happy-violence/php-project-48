<?php

namespace App\Parsers;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function readFile(string $filePath): string
{
    return is_readable($filePath)
        ? file_get_contents($filePath)
        : throw new \Exception("'{$filePath}' is not readable");
}

function parseJson(string $fileContent): object
{
    return json_decode($fileContent);
}

function parseYaml(string $fileContent): object
{
    return (object) Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
}
