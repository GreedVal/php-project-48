<?php

namespace Differ\Parser;

use Exception;
function parseJsonFile($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception("File not found: $filePath");
    }

    $jsonContent = file_get_contents($filePath);

    $data = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON decoding error: " . json_last_error_msg());
    }

    return $data;
}