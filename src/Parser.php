<?php

namespace Differ\Parser;

use Exception;
function parse($file)
{
    if (!file_exists($file)) {
        throw new Exception("File not found: $file");
    }

    $json = file_get_contents($file);

    $data = json_decode($json, true);

    return $data;
}