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

function makeDiff(array $content1, array $content2)
{
    $keys = array_unique(array_merge(array_keys($content1), array_keys($content2)));
    sort($keys);
    return checkDiff($keys, $content1, $content2);
}

function checkDiff(mixed $keys, array $content1, array $content2)
{
    $result = [];

    foreach ($keys as $key) {
        if (array_key_exists($key, $content1) && !array_key_exists($key, $content2)) {
            $result[$key] = ['status' => 'removed', 'value' => $content1[$key]];
        } elseif (!array_key_exists($key, $content1) && array_key_exists($key, $content2)) {
            $result[$key] = ['status' => 'added', 'value' => $content2[$key]];
        } elseif ($content1[$key] === $content2[$key]) {
            $result[$key] = ['status' => 'unchanged', 'value' => $content1[$key]];
        } else {
            $result[$key] = [
                'status' => 'modified',
                'oldValue' => $content1[$key],
                'newValue' => $content2[$key]
            ];
        }
    }

    return $result;
}