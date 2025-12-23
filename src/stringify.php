<?php

namespace App\Stringify;

require_once __DIR__ . '/../vendor/autoload.php';

function stringify(mixed $item): string
{
    if (gettype($item) === 'boolean') {
        return $item ? 'true' : 'false';
    }

    if (gettype($item) === 'integer') {
        return $item;
    }

    if (gettype($item) === 'string') {
        return $item;
    }

    if ($item === null) {
        return 'null';
    }

//    if (gettype($item) === 'array') {
//        //var_dump($item);die;
//        return 'fff';
//    }
    /*if (gettype($item) === 'object') {
        $result = [];
        foreach ($item as $key => $value) {
            $spacesCount = 4;
            $replacer = ' ';
            $indent = str_repeat($replacer, $depth * $spacesCount - $specialSymbol = 0);
            $result[] = "{$indent}  {$key}: " . stringify($value, $depth + 1);
            return "{\n" . implode("\n", $result) . "\n{$indent}}";
        }
    }*/

    return $item;
}
