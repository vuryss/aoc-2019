<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-3'));
$input = explode("\n", $input);
$start = microtime(true);

$wires = [];
$paths = [];

foreach ($input as $index => $wire) {
    $wire = explode(',', $wire);
    $x = $y = $s = 0;

    foreach ($wire as $move) {
        $dir = $move[0];
        $steps = (int) substr($move, 1);

        switch ($dir) {
            case 'R':
                for ($i = 0; $i < $steps; $i++) {
                    $paths[$index][$y . '.' . ++$x] = ++$s;
                }
                break;
            case 'L':
                for ($i = 0; $i < $steps; $i++) {
                    $paths[$index][$y . '.' . --$x] = ++$s;
                }
                break;
            case 'D':
                for ($i = 0; $i < $steps; $i++) {
                    $paths[$index][++$y . '.' . $x] = ++$s;
                }
                break;
            case 'U':
                for ($i = 0; $i < $steps; $i++) {
                    $paths[$index][--$y . '.' . $x] = ++$s;
                }
                break;
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
