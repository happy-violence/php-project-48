<?php

namespace Differ\Formatters\Json;

function render(array $comparisons): string
{
    $encoded = json_encode($comparisons);
    if ($encoded === false) {
        throw new \Exception("Error encoding data to JSON: " . json_last_error_msg());
    }
    return $encoded;
}
