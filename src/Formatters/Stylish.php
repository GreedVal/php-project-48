<?php

namespace Differ\Formatters\Stylish;

use Exception;

function stylishFormat(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    return "{\n" . implode("\n", $formattedDiff) . "\n}";
}

function makeStringsFromDiff(array $diff, int $level = 0): array
{
    $spaces = getSpaces($level);
    $nextLevel = $level + 1;

    return array_map(function ($node) use ($spaces, $nextLevel) {
        return formatNode($node, $spaces, $nextLevel);
    }, $diff);
}

function formatNode(array $node, string $spaces, int $nextLevel): string
{
    ['status' => $status, 'key' => $key] = $node;

    $strValue = stringifyValue($node['value1'], $nextLevel);
    $formattedString = '';

    switch ($status) {
        case 'nested':
            $formattedString = formatNested($key, $node['value1'], $spaces, $nextLevel);
            break;
        case 'same':
            $formattedString = "{$spaces}    {$key}: {$strValue}";
            break;
        case 'added':
            $formattedString = "{$spaces}  + {$key}: {$strValue}";
            break;
        case 'removed':
            $formattedString = "{$spaces}  - {$key}: {$strValue}";
            break;
        case 'updated':
            $formattedString = formatUpdated($node, $spaces, $nextLevel);
            break;
        default:
            throw new Exception("NAN error stylish format");
    }

    return $formattedString;
}

function formatNested(string $key, array $value, string $spaces, int $nextLevel): string
{
    $nested = makeStringsFromDiff($value, $nextLevel);
    return "{$spaces}    {$key}: {\n" . implode("\n", $nested) . "\n{$spaces}    }";
}

function formatUpdated(array $node, string $spaces, int $nextLevel): string
{
    $key = $node['key'];
    $oldValue = stringifyValue($node['value1'], $nextLevel);
    $newValue = stringifyValue($node['value2'], $nextLevel);
    return "{$spaces}  - {$key}: {$oldValue}\n{$spaces}  + {$key}: {$newValue}";
}

function getSpaces(int $level): string
{
    return str_repeat('    ', $level);
}

function stringifyValue(mixed $value, int $level): mixed
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $result = convertArrayToString($value, $level);
        $spaces = getSpaces($level);
        return "{{$result}\n{$spaces}}";
    }

    return $value;
}

function convertArrayToString(array $value, int $level): string
{
    $nextLevel = $level + 1;
    return implode('', array_map(function ($key) use ($value, $nextLevel) {
        return "\n" . getSpaces($nextLevel) . "{$key}: " . stringifyValue($value[$key], $nextLevel);
    }, array_keys($value)));
}
