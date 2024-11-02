<?php

namespace Differ\Formatters\Plain;

function plainFormat(array $diff)
{
    $result = [];
    foreach ($diff as $key => $info) {
        switch ($info['status']) {
            case 'added':
                $result[] = "Property '{$key}' was added with value: '{$info['value']}'";
                break;
            case 'removed':
                $result[] = "Property '{$key}' was removed";
                break;
            case 'modified':
                $result[] = "Property '{$key}' was updated. From '{$info['oldValue']}' to '{$info['newValue']}'";
                break;
        }
    }
    return implode("\n", $result);
}