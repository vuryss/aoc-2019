<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-4'));
$input = explode('-', $input);
$start = microtime(true);

$part1 = $part2 = 0;

for ($i = (int) $input[0], $to = (int) $input[1]; $i <= $to; $i++) {
    $s = (string) $i;

    for ($j = 0, $len = strlen($s) - 1; $j < $len; $j++) {
        if ($s[$j] > $s[$j + 1]) continue 2;
    }

    $chars = count_chars($i, 1);

    if (preg_match('/(\d)\1/', $s)) {
        $part1++;

        if (array_search(2, count_chars($i, 1), true) !== false) {
            $part2++;
        }
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
