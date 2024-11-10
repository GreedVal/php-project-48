<?php

namespace Differ\Formatters\Plain;

use Exception;

function plainFormat(array $diff): string
{
    $formattedDiff = formatDiff($diff);
    return implode("\n", $formattedDiff);
}

function formatDiff(array $diff, string $parentKey = ''): array
{
    $result = [];

    if (empty($diff)) {
        return $result;
    }

    $node = array_shift($diff);
    $parentKeyTwo = $parentKey ? '.' : '';
    $key = "{$parentKey}{$parentKeyTwo}{$node['key']}";

    $strValue1 = stringifyValue($node['value1'] ?? null);
    $strValue2 = stringifyValue($node['value2'] ?? null);

    switch ($node['status']) {
        case 'nested':
            $nestedResult = formatDiff($node['value1'], $key);
            $result = array_merge($result, $nestedResult);
            break;
        case 'added':
            $result[] = "Property '{$key}' was added with value: {$strValue1}";
            break;
        case 'removed':
            $result[] = "Property '{$key}' was removed";
            break;
        case 'updated':
            $result[] = "Property '{$key}' was updated. From {$strValue1} to {$strValue2}";
            break;
        case 'same':
            break;
        default:
            throw new Exception("NAN error plain");
    }
    return array_merge($result, formatDiff($diff, $parentKey));
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
