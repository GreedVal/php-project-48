<?php

namespace Differ\Formatters\Json;

function format(array $diff)
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
