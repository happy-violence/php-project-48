<?php

namespace App\Differ;

require_once __DIR__ . '/../vendor/autoload.php';

function genDiff(array $data1, array $data2): string
{
    {
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

        return json_encode($result, JSON_PRETTY_PRINT);
    }
}
