<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use function App\Parsers\parseJson;
use function App\Parsers\parseYaml;
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

function sortByKeys(array $collection): array
{
    return Collection\sortBy($collection, function ($value) use ($key) { return asort($key); });
}

function genDiff(string $filePath1, string $filePath2): array
{
    //todo: исправить возвращаемое значение с array на string
    $fileContent1 = readFile($filePath1);
    //var_dump($fileContent1);die;
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

    $keys1 = (get_object_vars($data1));
    $keys2 = (get_object_vars($data2));
    $keys = array_merge($keys1, $keys2);

}
