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

    if (gettype($item) === 'integer') {
        return (string) $item;
    }

    return isComplexValue($item) ? "[complex value]" : $item;
}

function render(array $comparisons, string $parentKey = ''): string
{
    $filteredComparisons = array_filter($comparisons, fn ($comparison) => $comparison['status'] !== 'unchanged');
    $result = array_map(
        function (mixed $comparison) use ($parentKey) {
            if (!empty($parentKey)) {
                $childrenKey = "{$parentKey}.{$comparison['key']}";
            } else {
                $childrenKey = $comparison['key'];
            }

            $oldValue = stringify($comparison['oldValue']);
            $newValue = stringify($comparison['newValue']);

            return match ($comparison['status']) {
                'nested' => render($comparison['children'], $childrenKey),
                'added' => "Property '{$childrenKey}' was added with value: " . stringify($comparison['newValue']),
                'deleted' => "Property '{$childrenKey}' was removed",
                'changed' => "Property '{$childrenKey}' was updated. From {$oldValue} to {$newValue}",
            };
        },
        $filteredComparisons
    );

    return implode("\n", $result);
}
