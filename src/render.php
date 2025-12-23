<?php

namespace App\Render;

require_once __DIR__ . '/../vendor/autoload.php';

use function App\Stringify\stringify;

function isNestedStructure($item): bool
{
    return is_array($item);
    //todo добавить проверки на то, что во вложенных массивах есть признаки (ключ статус, ключи value или newValue...
}

function getFormattedString(string $key, mixed $value, string $sign, int $depth, string $indent): string
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
    $specialSymbol = 2;
    $indent = str_repeat($replacer, $depth * $spacesCount - $specialSymbol);

    foreach ($comparisons as $comparison) {
        if ($comparison['status'] === 'nested') {
            $result[] = "{$indent}  {$comparison['key']}: " . render($comparison['children'], $depth + 1);
        } else {
            if ($comparison['status'] === 'changed') {
                $result[] = getFormattedString($comparison['key'], $comparison['oldValue'], '-',$depth, $indent);
                $result[] = getFormattedString($comparison['key'], $comparison['newValue'], '+',$depth, $indent);
            }

            if ($comparison['status'] === 'unchanged') {
                $result[] = getFormattedString($comparison['key'], $comparison['value'], ' ',$depth, $indent);
            }

            if ($comparison['status'] === 'deleted') {
                $result[] = getFormattedString($comparison['key'], $comparison['oldValue'], '-',$depth, $indent);
            }

            if ($comparison['status'] === 'added') {
                $result[] = getFormattedString($comparison['key'], $comparison['newValue'], '+',$depth, $indent);
            }
        }
    }

    $indentForClosedBrace = str_repeat($replacer, ($depth - 1) * $spacesCount);

    return "{\n" . implode("\n", $result) . "\n{$indentForClosedBrace}}";
}
