<?php

namespace App\Render;

require_once __DIR__ . '/../vendor/autoload.php';

use function App\Stringify\stringify;

function isNestedStructure($item): bool
{
    return is_array($item);
    //&&
        //array_key_exists('status', $item) &&
        //(array_key_exists('value', $item) || array_key_exists('newValue', $item) || array_key_exists('oldValue', $item));
}

function getFormattedString(string $sign, string $key, mixed $value, int $depth, string $indent): string
{
    if (isNestedStructure($value)) {
        $value = render($value, $depth + 1);
    }

    return "{$indent}{$sign} {$key}: " . stringify($value);
}

function render(array $comparisons, int $depth = 1): string
{
    $result = [];
    $spacesCount = 4;
    $replacer = ' ';
    $indent = str_repeat($replacer, $depth * $spacesCount - $specialSymbol = 2);

    foreach ($comparisons as $comparison) {
        if ($comparison['status'] === 'nested') {
            $result[] = "{$indent}  {$comparison['key']}: " . render($comparison['children'], $depth + 1);
        } else {
            $specialSymbol = 2;
            $indent = str_repeat($replacer, $depth * $spacesCount - $specialSymbol);

            if ($comparison['status'] === 'changed') {
                if (isNestedStructure($comparison['oldValue'])) {
                    $oldValue = render($comparison['oldValue'], $depth + 1);
                } else {
                    $oldValue = $comparison['oldValue'];
                }

                $result[] = "{$indent}- {$comparison['key']}: " . stringify($oldValue);

                if (isNestedStructure($comparison['newValue'])) {
                    $newValue = render($comparison['newValue'], $depth + 1);
                } else {
                    $newValue = $comparison['newValue'];
                }

                $result[] = "{$indent}+ {$comparison['key']}: " . stringify($newValue);
            }

            if ($comparison['status'] === 'unchanged') {
                if (isNestedStructure($comparison['value'])) {
                    $value = render($comparison['value'], $depth + 1);
                } else {
                    $value = $comparison['value'];
                }

                $result[] = "{$indent}  {$comparison['key']}: " . stringify($value);
            }

            if ($comparison['status'] === 'deleted') {
                if (isNestedStructure($comparison['oldValue'])) {
                    $oldValue = render($comparison['oldValue'], $depth + 1);
                } else {
                    $oldValue = $comparison['oldValue'];
                }

                $result[] = "{$indent}- {$comparison['key']}: " . stringify($oldValue);
            }

            if ($comparison['status'] === 'added') {
                if (isNestedStructure($comparison['newValue'])) {
                    $newValue = render($comparison['newValue'], $depth + 1);
                } else {
                    $newValue = $comparison['newValue'];
                }

                $result[] = "{$indent}+ {$comparison['key']}: " . stringify($newValue);
            }
        }
    }

    $indentForClosedBrace = str_repeat($replacer, ($depth - 1) * $spacesCount);

    return "{\n" . implode("\n", $result) . "\n{$indentForClosedBrace}}";
}
