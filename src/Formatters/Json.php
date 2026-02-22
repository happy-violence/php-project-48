<?php

namespace Differ\Formatters\Json;

function render(array $comparisons): string
{
    return json_encode($comparisons) ? json_encode($comparisons) :
        throw new \Exception("Error encoding data to JSON: " . json_last_error_msg());
}
