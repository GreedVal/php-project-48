<?php

namespace Differ\Formatters\Stylish;

function stylishFormat(array $diff)
{
    $result = "{\n";
    foreach ($diff as $key => $info) {
        switch ($info['status']) {
            case 'added':
                $result .= "  + {$key}: {$info['value']}\n";
                break;
            case 'removed':
                $result .= "  - {$key}: {$info['value']}\n";
                break;
            case 'unchanged':
                $result .= "    {$key}: {$info['value']}\n";
                break;
            case 'modified':
                $result .= "  - {$key}: {$info['oldValue']}\n  + {$key}: {$info['newValue']}\n";
                break;
        }
    }
    $result .= "}";
    return $result;
}