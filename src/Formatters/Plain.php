<?php

namespace Differ\Formatters\Plain;

function isComplexValue(mixed $value): bool
{
    return is_object($value);
}

function stringify(mixed $item): string
{
    if (gettype($item) === 'boolean') {
        return $item ? 'true' : 'false';
    }

    if (gettype($item) === 'integer') {
        return $item;
    }

    if (gettype($item) === 'string') {
        return "'{$item}'";
    }

    if ($item === null) {
        return 'null';
    }

    return isComplexValue($item) ? "[complex value]" : $item;
}

function render(array $comparisons, string $parentKey = ''): string
{
    $filteredComparisons = array_filter($comparisons, fn ($comparison) => $comparison['status'] !== 'unchanged');
    $result = array_map(
        function (mixed $comparison) use ($parentKey) {
            if ($comparison['status'] === 'unchanged') {
                return null;
            }

            if (!empty($parentKey)) {
                $childrenKey = "{$parentKey}.{$comparison['key']}";
            } else {
                $childrenKey = $comparison['key'];
            }

            if ($comparison['status'] === 'nested') {
                return render($comparison['children'], $childrenKey);
            }

            return match ($comparison['status']) {
                'added' => "Property '{$childrenKey}' was added with value: " . stringify($comparison['newValue']),
                'deleted' => "Property '{$childrenKey}' was removed",
                'changed' => "Property '{$childrenKey}' was updated. From " .
                    stringify($comparison['oldValue']) . " to " . stringify($comparison['newValue']),
            };
        },
        $filteredComparisons
    );

    return implode("\n", $result);
}
