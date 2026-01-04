<?php

namespace App\Formatter;

use function App\Stringify\stringify;

function isComplexValue(mixed $value): bool
{
    return is_array($value);
}

function getValue(mixed $value): string
{
    if (gettype($value) === 'boolean' || $value === 'null') {
        return stringify($value);
    }
    if (gettype($value) === 'string') {
        return "'{$value}'";
    }

    return isComplexValue($value) ? "[complex value]" : stringify($value);
}

function renderForPlain(array $comparisons, string $parentKey = ''): string
{
    $array = array_reduce(
        $comparisons,
        function ($acc, $comparison) use ($parentKey) {
            if ($comparison['status'] === 'unchanged') {
                return $acc;
            }

            if (!empty($parentKey)) {
                $childrenKey = "{$parentKey}.{$comparison['key']}";
            } else {
                $childrenKey = $comparison['key'];
            }

            if ($comparison['status'] === 'nested') {
                $acc[] = renderForPlain($comparison['children'], $childrenKey);
                return $acc;
            }

            $acc[] = match ($comparison['status']) {
                'added' => "Property '{$childrenKey}' was added with value: " . getValue($comparison['newValue']),
                'deleted' => "Property '{$childrenKey}' was removed",
                'changed' => "Property '{$childrenKey}' was updated. From " .
                    getValue($comparison['oldValue']) . " to " . getValue($comparison['newValue']),
            };

            return $acc;
        },
        []
    );

    return implode("\n", $array);
}
