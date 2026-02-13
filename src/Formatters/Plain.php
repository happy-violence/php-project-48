<?php

namespace Differ\Formatters\Plain;

function isComplexValue(mixed $value): bool
{
    return is_object($value) || is_array($value);
}

function stringify(mixed $item): string
{
    if (is_bool($item)) {
        return $item ? 'true' : 'false';
    }

    if (is_string($item)) {
        return "'{$item}'";
    }

    if ($item === null) {
        return 'null';
    }

    if (is_int($item)) {
        return (string) $item;
    }

    return isComplexValue($item) ? "[complex value]" : $item;
}

function iter(array $comparisons, string $ancestry = '', $depth = 0): string
{
    $filteredComparisons = array_filter($comparisons, fn ($node) => $node['status'] !== 'unchanged');
    $result = array_map(
        function (mixed $node) use ($ancestry, $depth) {
            $childrenKey = !empty($ancestry) ? "$ancestry.{$node['key']}" : $node['key'];
            
            return match ($node['status']) {
                'nested' => iter($node['children'], $childrenKey, $depth + 1),
                'added' => "Property '{$childrenKey}' was added with value: " . stringify($node['newValue'] ?? null),
                'deleted' => "Property '{$childrenKey}' was removed",
                'changed' => "Property '{$childrenKey}' was updated. From " . stringify($node['oldValue'] ?? null)
                    . " to " . stringify($node['newValue'] ?? null),
                default => '',
            };
        },
        $filteredComparisons
    );

    return implode("\n", $result);
}

function render($tree): string
{
    return iter($tree, '', 0);
}
