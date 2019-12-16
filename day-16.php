<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-16'));
$start = microtime(true);

$pattern = [0, 1, 0, -1];

// Part 1
$len = strlen($input);
$copy = str_split($input);
for ($p = 0; $p < 100; $p++) {
    $output = [];
    for ($i = 0; $i < $len; $i++) {
        $result = 0;
        foreach (getPattern($pattern, $i + 1) as $key => $mult) {
            if (!isset($copy[$key])) break;
            $result += $copy[$key] * $mult;
        }
        $output[] = substr((string)$result, -1);
    }
    $copy = $output;
}

echo 'Part 1: ' . implode('', array_slice($copy, 0, 8)). PHP_EOL;

// Part 2
$input = str_repeat($input, 10000);
$input = str_split($input);
$offset = (int) implode('', array_slice($input, 0, 7));
$input = array_slice($input, $offset);
$len = count($input);

for ($p = 0; $p < 100; $p++) {
    $result = [];
    $result[$len] = 0;

    for ($i = $len - 1; $i >= 0; $i--) {
        $result[$i] = ($input[$i] + $result[$i + 1]) % 10;
    }

    $input = $result;
}

ksort($input);
$result = implode('', array_slice($input, 0, 8));

echo 'Part 2: ' . $result . PHP_EOL;

function getPattern(array $pattern, int $output) {
    $first = true;
    $key = $output - 1;

    while (true) {
        foreach ($pattern as $num) {
            for ($i = 0; $i < $output; $i++) {
                if ($first && $num === 0) {
                    continue;
                }
                $first = false;

                yield $key => $num;
                $key++;
            }
        }
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
