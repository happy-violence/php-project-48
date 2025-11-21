<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

use function App\Parsers\parseJson;
use function App\Parsers\parseYaml;
use function App\Parsers\readFile;
use Funct\Collection;

function getAbsolutePath(string $path): string
{
    return (str_starts_with($path, '/') ? $path : __DIR__ . '/../' . $path);
}

function getExtension(string $path): string
{
    $pathInfo = pathinfo($path);
    return $pathInfo['extension'];
}

function genDiff(string $filePath1, string $filePath2): array
{
    //todo: исправить возвращаемое значение с array на string
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

    /*$result = [];
    foreach ($data1 as $key1 => $value1) {
        if (array_key_exists($key1, $data2)) {
            if ($value1 === $data2[$key1]) {
                $newKey1 = "  {$key1}";
                $result[$newKey1] = $value1;
            } else {
                $newKey1 = "- {$key1}";
                $newKey2 = "+ {$key1}";
                $result[$newKey1] = $value1;
                $result[$newKey2] = $data2[$key1];
            }
        } else {
            $newKey1 = "- {$key1}";
            $result[$newKey1] = $value1;
        }
    }

    foreach ($data2 as $key2 => $value2) {
        if (!array_key_exists($key2, $data1)) {
            $newKey2 = "+ {$key2}";
            $result[$newKey2] = $value2;
        }
    }

    return str_replace('"', '', json_encode($result, JSON_PRETTY_PRINT));*/

    $keysAndValues1 = (get_object_vars($data1));
    $keysAndValues2 = (get_object_vars($data2));
    $keys1 = array_keys($keysAndValues1);
    $keys2 = array_keys($keysAndValues2);
    $keys = array_unique(array_merge($keys1, $keys2));
    sort($keys);
    $sortedKeys = Collection\sortBy($keys, function ($key) { return $key; });

    $result = array_map(function ($key) use ($keysAndValues1, $keysAndValues2) {
        $result = [];
        $sign1 = '  ';
        $sign2 = '  ';
        $sign = '  ';

        if (array_key_exists($key, $keysAndValues1) && array_key_exists($key, $keysAndValues2)) {
            if ($keysAndValues1[$key] !== $keysAndValues2[$key]) {
                $sign1 = '- ';
                $result[$sign1 . $key] = $keysAndValues1[$key];
                $sign2 = '+ ';
                $result[$sign2 . $key] = $keysAndValues2[$key];
            } else {
                $result[$sign . $key] = $keysAndValues1[$key];
            }
        }

        if (!array_key_exists($key, $keysAndValues2)) {
            $sign1 = '- ';
            $result[$sign1 . $key] = $keysAndValues1[$key];
        }

        if (!array_key_exists($key, $keysAndValues1)) {
            $sign2 = '+ ';
            $result[$sign2 . $key] = $keysAndValues2[$key];
        }

        return $result;
    }, $sortedKeys);

    return $result;
}
