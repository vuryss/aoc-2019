<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-6'));
$input = explode("\n", $input);
$start = microtime(true);

$orbits = [];
$orbitedBy = [];

foreach ($input as $data) {
    $a = explode(')', $data);

    $orbits[$a[1]] = $a[0];
    $orbitedBy[$a[0]][] = $a[1];
}

$count = 0;

foreach ($orbits as $moon => $planet) {
    $count++;

    while (isset($orbits[$planet])) {
        $count++;
        $planet = $orbits[$planet];
    }
}

echo 'Part 1: ' . $count . PHP_EOL;

$queue = [[$orbits['YOU'], 0]];
$passed = ['YOU'];

do {
    [$planet, $steps] = array_shift($queue);
    $next = [];

    if (isset($orbits[$planet]) && !in_array($orbits[$planet], $passed)) {
        $next[] = $orbits[$planet];
    }

    foreach ($orbitedBy[$planet] ?? [] as $item) {
        if (!in_array($item, $passed)) {
            $next[] = $item;
        }
    }

    foreach ($next as $item) {
        if ($item === $orbits['SAN']) {
            echo 'Part 2: ' .  ($steps + 1) . PHP_EOL;
            break 2;
        }

        array_push($queue, [$item, $steps + 1]);
    }

    $passed[] = $planet;
} while(true);

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
