<?php

namespace Differ\Formatters\Json;

function render(array $comparisons): false|string
{
    return json_encode($comparisons);
}
