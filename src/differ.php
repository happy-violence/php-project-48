<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

use function App\Parsers\parseJson;
use function App\Parsers\parseYaml;
use function App\Parsers\readFile;
use Funct\Collection;
use function App\Stringify\stringify;

function getAbsolutePath(string $path): string
{
    return (str_starts_with($path, '/') ? $path : __DIR__ . '/../' . $path);
}

function getExtension(string $path): string
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

    $keysAndValues1 = get_object_vars($data1);
    $data1 = array_map(fn($value) => stringify($value), $keysAndValues1);

    $keysAndValues2 = get_object_vars($data2);
    $data2 = array_map(fn($value) => stringify($value), $keysAndValues2);

    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $keys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = Collection\sortBy($keys, function ($key) { return $key; });

    $result = array_reduce($sortedKeys, function ($result, $key) use ($data1, $data2) {
        $sign = '  ';

        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if ($data1[$key] !== $data2[$key]) {
                $sign1 = '- ';
                $result[$sign1 . $key] = $data1[$key];
                $sign2 = '+ ';
                $result[$sign2 . $key] = $data2[$key];
            } else {
                $result[$sign . $key] = $data1[$key];
            }
        }

        if (!array_key_exists($key, $data2)) {
            $sign1 = '- ';
            $result[$sign1 . $key] = $data1[$key];
        }

        if (!array_key_exists($key, $data1)) {
            $sign2 = '+ ';
            $result[$sign2 . $key] = $data2[$key];
        }

        return $result;
    }, []);

    //return $result;
    $stringResult = '';
    foreach ($result as $key => $value) {
        $stringResult = $stringResult . $key . ': ' . $value . "\n";
    }
    return '{'. "\n" . $stringResult . '}';
}
