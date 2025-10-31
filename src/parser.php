<?php

namespace App\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

function parse(string $filePath): array
{
    $fileContent = file_get_contents($filePath);
    $array = json_decode($fileContent, true);
    ksort($array);
    return $array;
}
