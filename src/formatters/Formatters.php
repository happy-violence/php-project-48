<?php

namespace App\Formatter;

use function App\Formatter\renderForPlain;
use function App\Formatter\renderForStylish;
use function App\Formatter\renderForJson;

function chooseFormat(array $innerTree, string $formatName): string
{
    return match ($formatName) {
        'plain' => renderForPlain($innerTree),
        'stylish' => renderForStylish($innerTree),
        'json' => renderForJson($innerTree),
        default => throw new \Exception("Unknown format. Please choose stylish, plain or json format"),
    };
}
