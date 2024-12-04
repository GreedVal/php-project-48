<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(array $fileData): array
{
    switch ($fileData['format']) {
        case 'json':
            return json_decode($fileData['content'], true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($fileData['content']);
        default:
            throw new Exception("Unsupported format of file!");
    }
}
