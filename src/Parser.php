<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use function Differ\Differ\getFormat;
use function Differ\Differ\getFileData;

function parse(string $file): object
{
    $fileContent = getFileData($file);

    return match (getFormat($file)) {
        'json' => json_decode($fileContent),
        'yaml', 'yml' => (object) Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception("Extension " . getFormat($file) .
            "is not supported. Choose 'json', 'yaml' or 'yml' extension"),
    };
}
