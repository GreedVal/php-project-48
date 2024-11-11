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
    $result = [];

    if (is_array($value1) && is_array($value2)) {
        $result = getArrayConfig('nested', $uniqueKey, makeDiff($value1, $value2));
    } elseif (!array_key_exists($uniqueKey, $content1)) {
        $result = getArrayConfig('added', $uniqueKey, $value2);
    } elseif (!array_key_exists($uniqueKey, $content2)) {
        $result = getArrayConfig('removed', $uniqueKey, $value1);
    } elseif ($value1 === $value2) {
        $result = getArrayConfig('same', $uniqueKey, $value1);
    } else {
        $result = getArrayConfig('updated', $uniqueKey, $value1, $value2);
    }

    return $result;
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
    $uniqueKeys = array_values(array_unique(array_merge(array_keys($content1), array_keys($content2))));
    return recursiveSort($uniqueKeys);
}

function recursiveSort(array $array): array
{
    if (count($array) <= 1) {
        return $array;
    }

    $first = $array[0];
    $rest = array_slice($array, 1);

    $sortedRest = recursiveSort($rest);

    return insertSorted($first, $sortedRest);
}

function insertSorted(mixed $element, array $sortedArray): array
{
    if (count($sortedArray) === 0 || $element <= $sortedArray[0]) {
        return array_merge([$element], $sortedArray);
    }

    return array_merge([$sortedArray[0]], insertSorted($element, array_slice($sortedArray, 1)));
}
