<?php

namespace App\Formatter;

use function App\Formatter\renderForPlain;
use function App\Formatter\render;

function chooseFormat(array $innerTree, string $formatName): string
{
    return match ($formatName) {
        'plain' => renderForPlain($innerTree),
        'stylish' => render($innerTree),
        default => throw new \Exception("Unknown format. Please choose stylish or plain format"),
    };
}
