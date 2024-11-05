<?php

namespace Differ\Formatters\Plain;

function plainFormat(array $diff): string
{
    $formattedDiff = formatDiff($diff);
    return implode("\n", $formattedDiff);
}

function formatDiff(array $diff, string $parentKey = ''): array
{
    $result = [];

    foreach ($diff as $node) {
        $key = $parentKey . ($parentKey ? '.' : '') . $node['key'];

        switch ($node['status']) {
            case 'nested':
                $result = array_merge($result, formatDiff($node['value1'], $key));
                break;
            case 'added':
                $result[] = "Property '{$key}' was added with value: " . stringifyValue($node['value1']);
                break;
            case 'removed':
                $result[] = "Property '{$key}' was removed";
                break;
            case 'updated':
                $result[] = "Property '{$key}' was updated. From " . stringifyValue($node['value1']) . " to " . stringifyValue($node['value2']);
                break;
            case 'same':
                break;
        }
    }
    return $result;
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
    return "'{$value}'";
}
