<?php

namespace Differ\Formatters;

function chooseFormat(array $innerTree, string $formatName): string
{
    return match ($formatName) {
        'plain' => Plain\render($innerTree),
        'stylish' => Stylish\render($innerTree),
        'json' => Json\render($innerTree),
        default => throw new \Exception("Unknown format. Please choose stylish, plain or json format"),
    };
}
