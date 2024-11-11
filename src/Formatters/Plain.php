<?php

namespace Differ\Formatters\Plain;

use Exception;

use function Functional\flatten;

function plainFormat(array $diff): string
{
    $formattedDiff = formatDiff($diff);
    return implode("\n", $formattedDiff);
}

function formatDiff(array $diff, string $path = ''): array
{
    $result = [];

    $callback = function ($node) use ($path, &$result) {
        list('status' => $status, 'key' => $key, 'value1' => $value1, 'value2' => $value2) = $node;
        $fullPath = "{$path}{$key}";

        switch ($status) {
            case 'nested':
                $result = array_merge($result, formatDiff($value1, "{$path}{$key}."));
                break;
            case 'added':
                $stringifiedValue1 = stringifyValue($value1);
                $result[] = "Property '{$fullPath}' was added with value: {$stringifiedValue1}";
                break;
            case 'removed':
                $result[] = "Property '{$fullPath}' was removed";
                break;
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedValue2 = stringifyValue($value2);
                $result[] = "Property '{$fullPath}' was updated. From {$stringifiedValue1} to {$stringifiedValue2}";
                break;
            case 'same':
                break;
            default:
                throw new Exception("Unsupported format of file!");
        }
    };

    array_map($callback, $diff);

    $result = array_filter($result, function ($valueOfDifference) {
        return !is_null($valueOfDifference);
    });

    return $result;
}

function stringifyValue(mixed $value): string
{
    $result = '';

    if (is_null($value)) {
        $result = 'null';
    } elseif (is_bool($value)) {
        $result = $value ? 'true' : 'false';
    } elseif (is_array($value)) {
        $result = '[complex value]';
    } elseif (is_numeric($value)) {
        $result = (string) $value;
    } else {
        $result = "'{$value}'";
    }

    return $result;
}
