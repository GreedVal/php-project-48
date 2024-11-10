<?php

namespace Differ\Formatters;

use Exception;

use function Differ\Formatters\Json\jsonFormat;
use function Differ\Formatters\Plain\plainFormat;
use function Differ\Formatters\Stylish\stylishFormat;

function makeFormat(array $diff, string $format): string
{
    switch ($format) {
        case 'stylish':
            return stylishFormat($diff);
        case 'plain':
            return plainFormat($diff);
        case 'json':
            return jsonFormat($diff);
        default:
            throw new Exception("No format");
    }
}
