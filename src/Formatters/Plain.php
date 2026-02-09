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

function render(array $comparisons, string $parentKey = ''): string
{
    $filteredComparisons = array_filter($comparisons, fn ($node) => $node['status'] !== 'unchanged');
    $result = array_map(
        function (mixed $node) use ($parentKey) {
            if (!empty($parentKey)) {
                $childrenKey = "{$parentKey}.{$node['key']}";
            } else {
                $childrenKey = $node['key'];
            }

            $oldValue = stringify($node['oldValue']);
            $newValue = stringify($node['newValue']);

            return match ($node['status']) {
                'nested' => render($node['children'], $childrenKey),
                'added' => "Property '{$childrenKey}' was added with value: " . stringify($node['newValue']),
                'deleted' => "Property '{$childrenKey}' was removed",
                'changed' => "Property '{$childrenKey}' was updated. From {$oldValue} to {$newValue}",
            };
        },
        $filteredComparisons
    );

    return implode("\n", $result);
}
