<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(string $file): array
{
    $content = getFileContent($file);
    $path = pathinfo($file, PATHINFO_EXTENSION);

    switch ($path) {
        case 'json':
            return json_decode($content, true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($content);
        default:
            throw new Exception("Unsupported format of file!");
    }
}

function getFileContent(string $file): string
{
    if (!file_exists($file)) {
        throw new Exception("File not found: {$file}");
    }

    $content = file_get_contents($file);

    if ($content === false) {
        throw new Exception("Unable to read file: {$file}");
    }

    return $content;
}
