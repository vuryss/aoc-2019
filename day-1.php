<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-1'));
$input = explode("\n", $input);
$start = microtime(true);

$part1 = $part2 = 0;

foreach ($input as $item) {
    $value = (int)($item / 3) - 2;
    $part1 += $value;

    while ($value > 0) {
        $part2 += $value;
        $value = (int)($value / 3) - 2;
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
