<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylishFormat;
use function Differ\Formatters\Plain\plainFormat;
use function Differ\Formatters\Json\jsonFormat;

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
            exit("No format {$format}");
    }
}