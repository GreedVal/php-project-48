<?php

namespace Differ\Formatters\Stylish;

use Exception;

function format(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $implode = implode("\n", $formattedDiff);
    return "{\n{$implode}\n}";
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

    switch ($status) {
        case 'nested':
            return formatNested($key, $node['value1'], $spaces, $nextLevel);
        case 'same':
            return "{$spaces}    {$key}: {$strValue}";
        case 'added':
            return "{$spaces}  + {$key}: {$strValue}";
        case 'removed':
            return "{$spaces}  - {$key}: {$strValue}";
        case 'updated':
            return formatUpdated($node, $spaces, $nextLevel);
        default:
            throw new Exception("NAN error stylish format");
    }
}

function formatNested(string $key, array $value, string $spaces, int $nextLevel): string
{
    $nested = makeStringsFromDiff($value, $nextLevel);
    $implode = implode("\n", $nested);
    return "{$spaces}    {$key}: {\n{$implode}\n{$spaces}    }";
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
        $stringValue = stringifyValue($value[$key], $nextLevel);
        $spaces = getSpaces($nextLevel);
        return "\n{$spaces}{$key}: {$stringValue}";
    }, array_keys($value)));
}
