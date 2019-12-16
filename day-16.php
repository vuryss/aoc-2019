<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-16'));
$start = microtime(true);

$pattern = [0, 1, 0, -1];

// Part 1
$len = strlen($input);
$copy = str_split($input);
for ($p = 0; $p < 100; $p++) {
    $output = [];
    for ($i = $len; $i >= 1; $i--) {
        $sum = 0;
        for ($j = $len - 1, $to = $i - 1; $j >= $to; $j--) {
            $delta = (int) (($j + 1) / $i) % 4;
            $sum += $copy[$j] * $pattern[$delta];
        }
        $output[] = abs($sum) % 10;
    }
    $copy = array_reverse($output);
}

echo 'Part 1: ' . implode('', array_slice($copy, 0, 8)). PHP_EOL;

// Part 2
$offset = (int) substr($input, 0, 7);
$input = substr(str_repeat($input, 10000), $offset);
$input = str_split($input);
$len = count($input);

for ($p = 0; $p < 100; $p++) {
    $result = [$len => 0];

    for ($i = $len - 1; $i >= 0; $i--) {
        $result[$i] = ($input[$i] + $result[$i + 1]) % 10;
    }

    $input = $result;
}

$input = array_reverse(array_slice($input, -8));

echo 'Part 2: ' . implode('', $input) . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
