<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

use Exception;

function plainFormat(array $diff): string
{
    $formattedDiff = formatDiff($diff);
    return implode("\n", $formattedDiff);
}

function formatDiff(array $diff, string $path = ''): array
{
    $callback = function ($node) use ($path) {
        list('status' => $status, 'key' => $key, 'value1' => $value1, 'value2' => $value2) = $node;
        $fullPath = "{$path}{$key}";

        switch ($status) {
            case 'nested':
                return formatDiff($value1, "{$path}{$key}.");
            case 'added':
                $stringifiedValue1 = stringifyValue($value1);
                return "Property '{$fullPath}' was added with value: {$stringifiedValue1}";
            case 'removed':
                return "Property '{$fullPath}' was removed";
            case 'updated':
                $stringifiedValue1 = stringifyValue($value1);
                $stringifiedValue2 = stringifyValue($value2);
                return "Property '{$fullPath}' was updated. From {$stringifiedValue1} to {$stringifiedValue2}";
            case 'same':
                return;
            default:
                throw new Exception("Unsupported format of file!");
        }
    };
    $arrayOfDifferences = flatten(array_map($callback, $diff));
    return array_filter($arrayOfDifferences, function ($valueOfDifference) {
        return !is_null($valueOfDifference);
    });
}

function stringifyValue(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_numeric($value)) {
        return (string) $value;
    }
    return "'{$value}'";
}
