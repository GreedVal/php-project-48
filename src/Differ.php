<?php

namespace Differ\Differ;

use function Differ\Parser\parse;

function genDiff($file1, $file2)
{
    $data1 = parse($file1);
    $data2 = parse($file2);

    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($keys);

    $result = [];

    foreach ($keys as $key) {
        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if ($data1[$key] === $data2[$key]) {
                continue;
            } else {
                $result[] = "  - $key: " . json_encode($data1[$key], JSON_PRETTY_PRINT);
                $result[] = "  + $key: " . json_encode($data2[$key], JSON_PRETTY_PRINT);
            }
        } elseif (array_key_exists($key, $data1)) {
            $result[] = "  - $key: " . json_encode($data1[$key], JSON_PRETTY_PRINT);
        } else {
            $result[] = "  + $key: " . json_encode($data2[$key], JSON_PRETTY_PRINT);
        }
    }

    return "{\n" . implode("\n", $result) . "\n}";
}
