<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Differ\Differ\getExtension;
use function Differ\Differ\readFile;

function parse(string $file): object
{
    $fileContent = readFile($file);

    return match(getExtension($file)) {
        'json' => parseJson($fileContent),
        'yaml', 'yml' => parseYaml($fileContent),
        default => throw new \Exception("Extension ". getExtension($file) . "is not supported. Choose 'json', 'yaml' or 'yml' extension"),
    };
}

function parseJson(string $fileContent): mixed
{
    return json_decode($fileContent);
}

function parseYaml(string $fileContent): object
{
    return (object) Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
}
