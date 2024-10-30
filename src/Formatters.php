<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylishFormat;
use function Differ\Formatters\Plain\plainFormat;
use function Differ\Formatters\Json\jsonFormat;

function makeFormat(array $diff, string $format): string
{

    switch ($format) {
        case '':
            # code...
            break;

        default:
            # code...
            break;
    }


    return "";
}