<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Formatters\makeFormat;

function genDiff(string $file1, string $file2, string $formatName = 'stylish'): string
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
        return getArrayConfig('nested', $uniqueKey, makeDiff($value1, $value2));
    }
    if (!array_key_exists($uniqueKey, $content1)) {
        return getArrayConfig('added', $uniqueKey, $value2);
    }
    if (!array_key_exists($uniqueKey, $content2)) {
        return getArrayConfig('removed', $uniqueKey, $value1);
    }
    if ($value1 === $value2) {
        return getArrayConfig('same', $uniqueKey, $value1);
    }
    return getArrayConfig('updated', $uniqueKey, $value1, $value2);
}

function getArrayConfig(string $status, string $key, mixed $value1 = null, mixed $value2 = null)
{
    return [
        'status' => $status,
        'key' => $key,
        'value1' => $value1,
        'value2' => $value2
    ];
}

function getSortedUniqueKeys(array $content1, array $content2): array
{
    $mergedKeys = array_merge(array_keys($content1), array_keys($content2));
    $uniqueKeys = array_unique($mergedKeys);

    usort($uniqueKeys, fn($left, $right) => strcmp($left, $right));

    return $uniqueKeys;
}
