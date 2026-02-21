<?php

namespace Differ\Formatters\Stylish;

function makeIndent(int $depth, int $specialSymbol = 0): string
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

    if ($item === null) {
        return 'null';
    }

    if (gettype($item) !== 'object') {
        return (string)$item;
    }

    $properties = get_object_vars($item);

    $result = array_map(function ($key, $property) use ($depth) {
        $indent = makeIndent($depth + 1);
        $modifiedProperty = stringify($property, $depth + 1);
        return "{$indent}{$key}: {$modifiedProperty}";
    }, array_keys($properties), array_values($properties));

    $string = implode("\n", $result);
    $indent = makeIndent($depth);
    return "{\n{$string}\n{$indent}}";
}

function iter(array $comparisons, int $depth = 1): string
{
    $result = array_map(
        function ($node) use ($depth) {
            $key = $node['key'];
            $indent = makeIndent($depth, 2);
            $value = stringify($node['value'], $depth) ?? null;
            $oldValue = stringify($node['oldValue'], $depth) ?? null;
            $newValue = stringify($node['newValue'], $depth) ?? null;

            return match ($node['type']) {
                'nested' => (function () use ($indent, $key, $depth, $node) {
                    $nested = iter($node['children'], $depth + 1);
                    return "{$indent}  {$key}: {$nested}";
                })(),
                'added' => "{$indent}+ {$key}: {$newValue}",
                'deleted' => "{$indent}- {$key}: {$oldValue}",
                'changed' => "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: $newValue",
                'unchanged' => "{$indent}  {$key}: {$value}",
            };
        },
        $comparisons
    );

    $indentForClosedBrace = makeIndent($depth - 1);
    $string = implode("\n", $result);
    return "{\n{$string}\n{$indentForClosedBrace}}";
}

function render(array $tree): string
{
    return iter($tree, 1);
}
