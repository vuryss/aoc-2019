<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-7'));
$input = explode(',', $input);
$start = microtime(true);

$part1 = 0;

foreach (permutations([0,1,2,3,4]) as $comb) {
    $setting = str_split($comb);
    $signal = 0;
    for ($i = 0; $i < 5; $i++) {
        $comp = new IntCodeParser($input, $setting[$i], $signal);
        $signal = $comp->output();
    }

    if ($signal > $part1) $part1 = $signal;
}

$part2 = 0;

foreach (permutations([5,6,7,8,9]) as $comb) {
    $phase  = str_split($comb);
    $amps   = [];
    $signal = 0;

    for ($i = 0; $i < 5; $i++) {
        $amps[$i] = new IntCodeParser($input, $phase[$i]);
    }

    while (true) {
        for ($i = 0; $i < 5; $i++) {
            $amps[$i]->input($signal);
            $output = $amps[$i]->output();

            if ($output === null) {
                if ($signal > $part2) $part2 = $signal;
                break 2;
            }

            $signal = $output;
        }
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
