<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

use stdClass;
use function App\Parsers\parseJson;
use function App\Parsers\parseYaml;
use function App\Parsers\readFile;
use Funct\Collection;
use function App\Render\render;

function getAbsolutePath(string $path): string
{
    return (str_starts_with($path, '/') ? $path : __DIR__ . '/../' . $path);
}

function getExtension(string $path)
{
    $pathInfo = pathinfo($path);
    return $pathInfo['extension'];
}

function genDiff(string $filePath1, string $filePath2): string
{
    $fileContent1 = readFile($filePath1);
    $fileContent2 = readFile($filePath2);

    $data1 = null;
    $data2 = null;

    if (getExtension($filePath1) === 'json') {
        $data1 = parseJson($fileContent1);
    }

    if (getExtension($filePath1) === 'yaml' || getExtension($filePath1) === 'yml') {
        $data1 = parseYaml($fileContent1);
    }

    if (getExtension($filePath2) === 'json') {
        $data2 = parseJson($fileContent2);
    }

    if (getExtension($filePath2) === 'yaml' || getExtension($filePath2) === 'yml') {
        $data2 = parseYaml($fileContent2);
    }

    $result = getInnerTree($data1, $data2);
    //var_dump($result[0]['status']);die;
    return render($result);
}

function getInnerTree($data1, $data2)
{
    $data1 = get_object_vars($data1);
    $data2 = get_object_vars($data2);

    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $commonKeys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = Collection\sortBy($commonKeys, function ($key) {
        return $key;
    });

    $innerTree = array_reduce($sortedKeys, function ($innerTree, $key) use ($data1, $data2) {
        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if (!is_object($data1[$key]) || !is_object($data2[$key])) {
                if ($data1[$key] !== $data2[$key]) {
                    if (is_object($data1[$key])) {
                        $oldValue = getInnerTree($data1[$key], $data1[$key]);
                    } else {
                        $oldValue = $data1[$key];
                    }

                    if (is_object($data2[$key])) {
                        $newValue = getInnerTree($data2[$key], $data2[$key]);
                    } else {
                        $newValue = $data2[$key];
                    }

                    $status = 'changed';
                    $innerTree[] = ['key' => $key, 'oldValue' => $oldValue, 'newValue' => $newValue, 'status' => $status];
                } else {
                    $value = $data1[$key];
                    $status = 'unchanged';
                    $innerTree[] = ['key' => $key, 'value' => $value, 'status' => $status];
                }
            } else {
                $children = getInnerTree($data1[$key], $data2[$key]);
                $status = 'nested';
                $innerTree[] = ['key' => $key, 'children' => $children, 'status' => $status];
            }
        }

        if (!array_key_exists($key, $data2)) {
            if (is_object($data1[$key])) {
                $oldValue = getInnerTree($data1[$key], $data1[$key]);
            } else {
                $oldValue = $data1[$key];
            }

            $status = 'deleted';
            $innerTree[] = ['key' => $key, 'oldValue' => $oldValue, 'status' => $status];
        }

        if (!array_key_exists($key, $data1)) {
            if (is_object($data2[$key])) {
                $newValue = getInnerTree($data2[$key], $data2[$key]);
            } else {
                $newValue = $data2[$key];
            }

            $status = 'added';
            $innerTree[] = ['key' => $key, 'newValue' => $newValue, 'status' => $status];
        }
        return $innerTree;
    }, []);

    return $innerTree;
}
