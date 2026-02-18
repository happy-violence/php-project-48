<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parse;
use function Differ\Formatters\format;

function genDiff(string $path1, string $path2, $outputFormat = 'stylish'): string
{
    $fileContent1 = getFileData($path1);
    $fileContent2 = getFileData($path2);

    $fileFormat1 = getFormat($path1);
    $fileFormat2 = getFormat($path2);

    $data1 = parse($fileContent1, $fileFormat1);
    $data2 = parse($fileContent2, $fileFormat2);

    $innerTree = buildInnerTree($data1, $data2);
    return format($innerTree, $outputFormat);
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
            return ['key' => $key, 'oldValue' => $data1[$key], 'type' => 'deleted'];
        }

        if (!array_key_exists($key, $data1)) {
            return ['key' => $key, 'newValue' => $data2[$key], 'type' => 'added'];
        }

        if ($data1[$key] !== $data2[$key]) {
            if (is_object($data1[$key]) && is_object($data2[$key])) {
                return ['key' => $key, 'children' => buildInnerTree($data1[$key], $data2[$key]), 'type' => 'nested'];
            } else {
                return [
                    'key' => $key,
                    'oldValue' => $data1[$key],
                    'newValue' => $data2[$key],
                    'type' => 'changed'
                ];
            }
        }

        return ['key' => $key, 'value' => $data1[$key], 'type' => 'unchanged'];
    },
        $sortedKeys);

    return $result;
}
