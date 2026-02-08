<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parse;
use function Differ\Formatters\format;

function genDiff(string $file1, string $file2, $formatName = 'stylish'): string
{
    $data1 = parse($file1);
    $data2 = parse($file2);

    $innerTree = buildInnerTree($data1, $data2);
    return format($innerTree, $formatName);
}

function getFormat(string $path)
{
    $pathInfo = pathinfo($path);
    return $pathInfo['extension'];
}

function getFileData(string $filePath): string
{
    return is_readable($filePath)
        ? file_get_contents($filePath)
        : throw new \Exception("'{$filePath}' is not readable");
}

function buildInnerTree($data1, $data2)
{
    $data1 = get_object_vars($data1);
    $data2 = get_object_vars($data2);

    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $commonKeys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = Collection\sortBy($commonKeys, fn ($key) => $key);

    $result = array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data2)) {
            $oldValue = $data1[$key];
            return ['key' => $key, 'oldValue' => $oldValue, 'status' => 'deleted'];
        }

        if (!array_key_exists($key, $data1)) {
            $newValue = $data2[$key];
            return ['key' => $key, 'newValue' => $newValue, 'status' => 'added'];
        }

        if ($data1[$key] !== $data2[$key]) {
            if (is_object($data1[$key]) && is_object($data2[$key])) {
                $children = buildInnerTree($data1[$key], $data2[$key]);
                return ['key' => $key, 'children' => $children, 'status' => 'nested'];
            } else {
                $oldValue = $data1[$key];
                $newValue = $data2[$key];

                return [
                    'key' => $key,
                    'oldValue' => $oldValue,
                    'newValue' => $newValue,
                    'status' => 'changed'
                ];
            }
        }

        return ['key' => $key, 'value' => $data1[$key], 'status' => 'unchanged'];
    },
        $sortedKeys);

    return $result;
}
