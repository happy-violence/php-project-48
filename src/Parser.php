<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $data, $format): object
{
    return match ($format) {
        'json' => json_decode($data),
        'yaml', 'yml' => (object) Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception(
            "Extension {$format} is not supported. Choose 'json', 'yaml' or 'yml' extension"
        ),
    };
}
