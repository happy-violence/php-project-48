<?php

namespace Differ\Formatters\Stylish;

function countIndent($depth, $specialSymbol = 0): string
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
        $depth += 1;
        $properties = get_object_vars($item);

        $result = array_map(function ($key, $property) use ($depth)  {
            return countIndent($depth) . $key . ': ' . stringify($property, $depth);
        }, array_keys($properties), array_values($properties));

        return "{\n" . implode("\n", $result) . "\n" . countIndent($depth - 1) . "}";
    }

    return $item;
}

function render(array $comparisons, int $depth = 1): string
{
    $result = [];
    $indent = countIndent($depth, 2);

    foreach ($comparisons as $comparison) {
        if ($comparison['status'] === 'nested') {
            $result[] = "{$indent}  {$comparison['key']}: " . render($comparison['children'], $depth + 1);
        } else {
            if ($comparison['status'] === 'changed') {
                $result[] = "{$indent}- " . stringify($comparison['key']) . ": " . stringify($comparison['oldValue'], $depth);
                $result[] = "{$indent}+ " . stringify($comparison['key']) . ": " . stringify($comparison['newValue'], $depth);
            }

            if ($comparison['status'] === 'unchanged') {
                $result[] = "{$indent}  " . stringify($comparison['key']) . ": " . stringify($comparison['value'], $depth);
            }

            if ($comparison['status'] === 'deleted') {
                $result[] = "{$indent}- " . stringify($comparison['key']) . ": " . stringify($comparison['oldValue'], $depth);
            }

            if ($comparison['status'] === 'added') {
                $result[] = "{$indent}+ " . stringify($comparison['key']) . ": " . stringify($comparison['newValue'], $depth);
            }
        }
    }

    $indentForClosedBrace = countIndent($depth - 1);
    return "{\n" . implode("\n", $result) . "\n{$indentForClosedBrace}}";
}
