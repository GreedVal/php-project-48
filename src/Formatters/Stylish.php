<?php

namespace Differ\Formatters\Stylish;

use Exception;

const INDENT_SIZE = 4;
const INDENT_SYMBOL = ' ';
const SIGN_INDENT_SIZE = 2;

function format(array $diff): string
{
    $formattedDiff = makeStringsFromDiff($diff);
    $implode = implode("\n", $formattedDiff);
    return "{\n{$implode}\n}";
}

function makeStringsFromDiff(array $diff, int $level = 0): array
{
    $nextLevel = $level + 1;

    return array_map(function ($node) use ($level, $nextLevel) {
        return formatNode($node, $level, $nextLevel);
    }, $diff);
}

function formatNode(array $node, int $level, int $nextLevel): string
{
    $baseSpaces = getSpaces($level);
    $signSpaces = getSpacesWithSign($level);

    ['status' => $status, 'key' => $key] = $node;

    switch ($status) {
        case 'nested':
            return formatNested($key, $node['value1'], $baseSpaces, $nextLevel);
        case 'same':
            $value = stringifyValue($node['value1'], $nextLevel);
            return "{$baseSpaces}{$key}: {$value}";
        case 'added':
            $value = stringifyValue($node['value1'], $nextLevel);
            return "{$signSpaces}+ {$key}: {$value}";
        case 'removed':
            $value = stringifyValue($node['value1'], $nextLevel);
            return "{$signSpaces}- {$key}: {$value}";
        case 'updated':
            return formatUpdated($node, $signSpaces, $nextLevel);
        default:
            throw new Exception("Unknown status in stylish format: {$status}");
    }
}

function formatNested(string $key, array $value, string $spaces, int $nextLevel): string
{
    $nested = makeStringsFromDiff($value, $nextLevel);
    $implode = implode("\n", $nested);
    return "{$spaces}{$key}: {\n{$implode}\n{$spaces}}";
}

function formatUpdated(array $node, string $signSpaces, int $nextLevel): string
{
    $key = $node['key'];
    $oldValue = stringifyValue($node['value1'], $nextLevel);
    $newValue = stringifyValue($node['value2'], $nextLevel);

    $removedLine = "{$signSpaces}- {$key}: {$oldValue}";
    $addedLine = "{$signSpaces}+ {$key}: {$newValue}";
    return "{$removedLine}\n{$addedLine}";
}

function getSpaces(int $level): string
{
    return str_repeat(INDENT_SYMBOL, INDENT_SIZE * $level);
}

function getSpacesWithSign(int $level): string
{
    return str_repeat(INDENT_SYMBOL, INDENT_SIZE * $level - SIGN_INDENT_SIZE);
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
        return "{\n{$result}\n{$spaces}}";
    }

    return $value;
}

function convertArrayToString(array $value, int $level): string
{
    $nextLevel = $level + 1;
    return implode("\n", array_map(function ($key) use ($value, $nextLevel) {
        $stringValue = stringifyValue($value[$key], $nextLevel);
        $spaces = getSpaces($nextLevel);
        return "{$spaces}{$key}: {$stringValue}";
    }, array_keys($value)));
}