<?php

namespace Differ\Formatters\Json;

function render(array $comparisons): string
{
    return json_encode($comparisons, JSON_PRETTY_PRINT);
}
