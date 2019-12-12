<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-12'));
$input = explode("\n", $input);
$start = microtime(true);

$moons = [];
$initial = [];
$id = 1;

foreach ($input as $item) {
    preg_match('/(-?\d+).+?(-?\d+).+?(-?\d+)/', $item, $matches);

    $moons[$id] = [
        'id' => $id,
        'pos' => [(int) $matches[1], (int) $matches[2], (int) $matches[3]],
        'vel' => [0, 0, 0],
    ];

    $initial[$id] = $moons[$id]['pos'];

    $id++;
}

$intervals = [];

for ($i = 1; count($intervals) != 3; $i++) {
    foreach (pairs($moons) as $pair) {
        foreach (range(0, 2) as $axis) {
            $diff = $pair[0]['pos'][$axis] <=> $pair[1]['pos'][$axis];
            $moons[$pair[0]['id']]['vel'][$axis] -= $diff;
            $moons[$pair[1]['id']]['vel'][$axis] += $diff;
        }
    }

    foreach ($moons as $id => $moon) {
        $moons[$id]['pos'][0] += $moons[$id]['vel'][0];
        $moons[$id]['pos'][1] += $moons[$id]['vel'][1];
        $moons[$id]['pos'][2] += $moons[$id]['vel'][2];
    }

    if ($i === 1000) {
        $total = 0;

        foreach ($moons as $id => $moon) {
            $a = abs($moon['pos'][0]) + abs($moon['pos'][1]) + abs($moon['pos'][2]);
            $b = abs($moon['vel'][0]) + abs($moon['vel'][1]) + abs($moon['vel'][2]);
            $total += $a * $b;
        }

        echo 'Part 1: ' . $total . PHP_EOL;
    }

    for ($j = 0; $j < 3; $j++) {
        if (isset($intervals[$j])) continue;

        if ($moons[1]['pos'][$j] === $initial[1][$j] && $moons[1]['vel'][$j] === 0
            && $moons[2]['pos'][$j] === $initial[2][$j] && $moons[2]['vel'][$j] === 0
            && $moons[3]['pos'][$j] === $initial[3][$j] && $moons[3]['vel'][$j] === 0
            && $moons[4]['pos'][$j] === $initial[4][$j] && $moons[4]['vel'][$j] === 0
        ) {
            $intervals[$j] = $i;
        }
    }
}

echo 'Part 2: ' . gmp_strval(gmp_lcm(gmp_lcm($intervals[0], $intervals[1]), $intervals[2])) . PHP_EOL;

function pairs($moons)
{
    while (count($moons) > 1) {
        $moon = array_splice($moons, 0, 1);
        $moon = $moon[0];

        foreach ($moons as $m) {
            yield [$moon, $m];
        }
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
