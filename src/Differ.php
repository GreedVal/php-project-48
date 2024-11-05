<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Formatters\makeFormat;

function genDiff($file1, $file2, $formatName = 'stylish'): string
{
    $content1 = parse($file1);
    $content2 = parse($file2);

    $diff = makeDiff($content1, $content2);

    return makeFormat($diff, $formatName);
}

function makeDiff(array $content1, array $content2): array
{
    $uniqueKeys = getSortedUniqueKeys($content1, $content2);

    $callback = function ($uniqueKey) use ($content1, $content2) {
        return checkDifference($uniqueKey, $content1, $content2);
    };

    return array_map($callback, $uniqueKeys);
}

function checkDifference(mixed $uniqueKey, array $content1, array $content2): array
{
    $value1 = $content1[$uniqueKey] ?? null;
    $value2 = $content2[$uniqueKey] ?? null;

    if (is_array($value1) && is_array($value2)) {
        return [
            'status' => 'nested',
            'key' => $uniqueKey,
            'value1' => makeDiff($value1, $value2),
            'value2' => null
        ];
    }
    if (!array_key_exists($uniqueKey, $content1)) {
        return [
            'status' => 'added',
            'key' => $uniqueKey,
            'value1' => $value2,
            'value2' => null
        ];
    }
    if (!array_key_exists($uniqueKey, $content2)) {
        return [
            'status' => 'removed',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => null
        ];
    }
    if ($value1 === $value2) {
        return [
            'status' => 'same',
            'key' => $uniqueKey,
            'value1' => $value1,
            'value2' => null
        ];
    }
    return [
        'status' => 'updated',
        'key' => $uniqueKey,
        'value1' => $value1,
        'value2' => $value2
    ];
}

function getSortedUniqueKeys(array $content1, array $content2): array
{
    $keys = array_unique(array_merge(array_keys($content1), array_keys($content2)));
    sort($keys);

    return $keys;
}