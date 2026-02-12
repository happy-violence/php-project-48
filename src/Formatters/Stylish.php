<?php

namespace Differ\Formatters\Stylish;

function makeIndent($depth, $specialSymbol = 0): string
{
    $spacesCount = 4;
    $replacer = ' ';
    return str_repeat($replacer, $depth * $spacesCount - $specialSymbol);
}

function stringify(mixed $item, int $depth = 1): string
{
    if (gettype($item) === 'boolean') {
        return $item ? 'true' : 'false';
    }

    if (gettype($item) === 'integer') {
        return $item;
    }

    if (gettype($item) === 'string') {
        return $item;
    }

    if ($item === null) {
        return 'null';
    }

    if (gettype($item) === 'object') {
        $properties = get_object_vars($item);

        $result = array_map(function ($key, $property) use ($depth) {
            return makeIndent($depth + 1) . $key . ': ' . stringify($property, $depth + 1);
        }, array_keys($properties), array_values($properties));

        return "{\n" . implode("\n", $result) . "\n" . makeIndent($depth) . "}";
    }

    return $item;
}

function iter(array $comparisons, int $depth = 1): string
{
    $result = array_map(
        function ($node) use ($depth) {
            $key = $node['key'];
            $indent = makeIndent($depth, 2);

            return match ($node['status']) {
                'nested' => makeIndent($depth) . "{$node['key']}: " . iter($node['children'], $depth + 1),
                'added' => "{$indent}+ {$key}: " . stringify($node['newValue'], $depth),
                'deleted' => "{$indent}- {$key}: " . stringify($node['oldValue'], $depth),
                'changed' => "{$indent}- {$key}: " . stringify($node['oldValue'], $depth) .
                    "\n{$indent}+ {$key}: " . stringify($node['newValue'], $depth),
                'unchanged' => "{$indent}  {$key}: " . stringify($node['value'], $depth),
            };
        },
        $comparisons
    );

    $indentForClosedBrace = makeIndent($depth - 1);
    return "{\n" . implode("\n", $result) . "\n{$indentForClosedBrace}}";
}

function render($tree): string
{
    return iter($tree, 1);
}
