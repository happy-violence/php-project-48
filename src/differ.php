<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

function getAbsolutePath(string $path): string
{
    return (str_starts_with($path, '/') ? $path : __DIR__ . '/../' . $path);
}

function readFile(string $filePath): string
{
    //todo: не забыть обрабатывать результат в случае ошибки (если файл не существует например)
    return is_readable($filePath)
        ? file_get_contents($filePath)
        : throw new \Exception("'{$filePath}' is not readable");
}

function parse(string $filePath): array
{
    $fileContent = readFile($filePath);
    $array = json_decode($fileContent, true);
    ksort($array);
    return $array;
}

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = parse($filePath1);
    $data2 = parse($filePath2);

    $result = [];
    foreach ($data1 as $key1 => $value1) {
        if (array_key_exists($key1, $data2)) {
            if ($value1 === $data2[$key1]) {
                $result[$key1] = $value1;
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

    return str_replace('"', '', json_encode($result, JSON_PRETTY_PRINT));
}
