<?php

$input = trim(file_get_contents('input/day-1'));
$input = explode("\n", $input);
$start = microtime(true);

$part1 = $part2 = 0;

foreach ($input as $item) {
    $value = (int)($item / 3) - 2;
    $part1 += $value;
    $module = $value;

    while (true) {
        $value = (int)($value / 3) - 2;
        if ($value <= 0) break;
        $module += $value;
    }

    $part2 += $module;
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
