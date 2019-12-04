<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-3'));
$input = explode("\n", $input);
$start = microtime(true);

$wires = $paths = [];
$dx = ['L' => -1, 'R' => 1, 'D' => 0, 'U' =>  0];
$dy = ['L' =>  0, 'R' => 0, 'D' => 1, 'U' => -1];

foreach ($input as $index => $wire) {
    $wire = explode(',', $wire);
    $x = $y = $s = 0;

    foreach ($wire as $move) {
        $dir = $move[0];
        for ($i = 0, $steps = (int) substr($move, 1); $i < $steps; $i++) {
            $x += $dx[$dir];
            $y += $dy[$dir];
            $paths[$index][$y . '.' . $x] = ++$s;
        }
    }
}

$intersections = array_intersect_key($paths[0], $paths[1]);
$sums1 = $sums2 = [];

foreach ($intersections as $key => $intersection) {
    [$x, $y] = explode('.', $key);
    $sums1[] = abs($x) + abs($y);
    $sums2[] = $paths[0][$key] + $paths[1][$key];
}

echo 'Part 1: ' . min($sums1) . PHP_EOL;
echo 'Part 2: ' . min($sums2) . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
