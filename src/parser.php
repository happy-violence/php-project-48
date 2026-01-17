<?php

namespace App\Parsers;

use Symfony\Component\Yaml\Yaml;

use function App\Differ\getExtension;

function parse(string $file): object
{
    $fileContent = readFile($file);
    //var_dump($fileContent);die;
    //var_dump($file);die;

    return match(getExtension($file)) {
        'json' => parseJson($fileContent),
        'yaml', 'yml' => parseYaml($fileContent),
        default => throw new \Exception("Extension ". getExtension($file) . "is not supported. Choose 'json', 'yaml' or 'yml' extension"),
    };
}

function parseJson(string $fileContent): mixed
{
    return json_decode($fileContent);
    //var_dump(json_decode($fileContent));die;
}

function parseYaml(string $fileContent): object
{
    return (object) Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
}
