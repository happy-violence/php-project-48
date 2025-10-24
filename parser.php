<?php

namespace Parser;

//require_once __DIR__ . '/../vendor/autoload.php';

function parse(string $filePath)
{
    //var_dump($filePath);
    $fileContent = file_get_contents($filePath);
    return json_decode($fileContent);
}
