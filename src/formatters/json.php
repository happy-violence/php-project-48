<?php

namespace App\Formatter;

function renderForJson(array $comparisons): string
{
    return json_encode($comparisons, JSON_PRETTY_PRINT);
}
