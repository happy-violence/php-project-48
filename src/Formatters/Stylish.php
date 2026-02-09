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
        $depth += 1;
        $properties = get_object_vars($item);

        $result = array_map(function ($key, $property) use ($depth) {
            return makeIndent($depth) . $key . ': ' . stringify($property, $depth);
        }, array_keys($properties), array_values($properties));

        return "{\n" . implode("\n", $result) . "\n" . makeIndent($depth - 1) . "}";
    }

    return $item;
}

function render(array $comparisons, int $depth = 1): string
{
    //$result = [];
    $indent = makeIndent($depth, 2);

    /*$result = array_map(
        function ($node) {
            $key = stringify($node['key']);

            return match ($node['status']) {
                'nested' => "{$indent}  {$node['key']}: " . render($node['children'], $depth + 1),
                'added' => "{$indent}+ {$key}: " . stringify($node['newValue'], $depth),
                'deleted' => "{$indent}- {$key}: " . stringify($node['oldValue'], $depth),
                //в changed нужно добавить 2 значения: oldValue и newValue
                'changed' => "{$indent}- {$key}: " . stringify($node['oldValue'], $depth) . "\n{$indent}+ {$key}: ". stringify($node['newValue'], $depth),
                //"{$indent}+ {$key}: " . stringify($node['newValue'], $depth)
            };
        },
        $comparisons
    );*/
    foreach ($comparisons as $comparison) {
        $key = stringify($comparison['key']);

        if ($comparison['status'] === 'nested') {
            $result[] = "{$indent}  {$key}: " . render($comparison['children'], $depth + 1);
        } else {
            if ($comparison['status'] === 'changed') {
                $result[] = "{$indent}- {$key}: " .
                    stringify($comparison['oldValue'], $depth);

                $result[] = "{$indent}+ {$key}: " .
                    stringify($comparison['newValue'], $depth);
            }

            if ($comparison['status'] === 'unchanged') {
                $result[] = "{$indent}  {$key}: " .
                    stringify($comparison['value'], $depth);
            }

            if ($comparison['status'] === 'deleted') {
                $result[] = "{$indent}- {$key}: " .
                    stringify($comparison['oldValue'], $depth);
            }

            if ($comparison['status'] === 'added') {
                $result[] = "{$indent}+ {$key}: " .
                    stringify($comparison['newValue'], $depth);
            }
        }
    }

    $indentForClosedBrace = makeIndent($depth - 1);
    return "{\n" . implode("\n", $result) . "\n{$indentForClosedBrace}}";
}
