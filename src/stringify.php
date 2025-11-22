<?php

namespace App\Stringify;

require_once __DIR__ . '/../vendor/autoload.php';
function withoutQuotes(string $item): string
{
    return str_replace('"', '', $item);
}

/*function stringify(mixed $item, string $replacer = ' ', int $spacesCount = 1)
{
    $replacers = str_repeat($replacer, $spacesCount);
    if (is_array($item)) {
        $result = [];
        foreach ($item as $key => $value) {
            $result[$replacers . withoutQuotes($key)] = withoutQuotes($value);
        }
        return $result;
    }
    return (string)$item;
}*/

function stringify(mixed $item): string
{
    if (gettype($item) === 'boolean') {
        return withoutQuotes($item ? 'true' : 'false');
    }
    return withoutQuotes($item);
}
