<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parse;
use function Differ\Formatters\chooseFormat;

function genDiff(string $file1, string $file2, $formatName = 'stylish'): string
{
    $data1 = parse($file1);
    $data2 = parse($file2);

    $innerTree = buildInnerTree($data1, $data2);
    return chooseFormat($innerTree, $formatName);
}

function getAbsolutePath(string $path): string
{
    return (str_starts_with($path, '/') ? $path : __DIR__ . '/../' . $path);
}

function getExtension(string $path)
{
    $pathInfo = pathinfo($path);
    return $pathInfo['extension'];
}

function readFile(string $filePath): string
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
    $sortedKeys = Collection\sortBy($commonKeys, function ($key) {
        return $key;
    });



    return array_map(function ($key) use ($data1, $data2) {

        if (!array_key_exists($key, $data2)) {
            if (is_object($data1[$key])) {
                $oldValue = buildInnerTree($data1[$key], $data1[$key]);
            } else {
                $oldValue = $data1[$key];
            }

            $status = 'deleted';
            return ['key' => $key, 'oldValue' => $oldValue, 'status' => $status];
        }

        if (!array_key_exists($key, $data1)) {
            if (is_object($data2[$key])) {
                $newValue = buildInnerTree($data2[$key], $data2[$key]);
            } else {
                $newValue = $data2[$key];
            }

            $status = 'added';
            return ['key' => $key, 'newValue' => $newValue, 'status' => $status];
        }

        if (!is_object($data1[$key]) || !is_object($data2[$key])) {
            if ($data1[$key] !== $data2[$key]) {
                if (is_object($data1[$key])) {
                    $oldValue = buildInnerTree($data1[$key], $data1[$key]);
                } else {
                    $oldValue = $data1[$key];
                }

                if (is_object($data2[$key])) {
                    $newValue = buildInnerTree($data2[$key], $data2[$key]);
                } else {
                    $newValue = $data2[$key];
                }

                $status = 'changed';
                return [
                    'key' => $key,
                    'oldValue' => $oldValue,
                    'newValue' => $newValue,
                    'status' => $status
                ];
            } else {
                $value = $data1[$key];
                $status = 'unchanged';
                return ['key' => $key, 'value' => $value, 'status' => $status];
            }
        } else {
            $children = buildInnerTree($data1[$key], $data2[$key]);
            $status = 'nested';
            return ['key' => $key, 'children' => $children, 'status' => $status];
        }
    },
        $sortedKeys);
}
