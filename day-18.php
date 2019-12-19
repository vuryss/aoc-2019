<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-18'));
$input = explode("\n", $input);
$start = microtime(true);

$map = [];
$myX = $myY = 0;
$keys = [];

foreach ($input as $y => $item) {
    $map[$y] = str_split($item);
    foreach ($map[$y] as $x => $value) {
        if ($value === '@') {
            [$myX, $myY] = [$x, $y];
            $map[$y][$x] = '.';
        } elseif (ctype_lower($value)) {
            $keys[$value] = [$x, $y];
        }
    }
}

// Down, Up, Right, Left
$directions = [[0, 1], [0, -1], [1, 0], [-1, 0]];
$keys = mapKeyPaths($map, $keys);

// Get all possible starting positions
$queue = new Ds\Deque();
$queue->push([$myX, $myY, 0]);

$set = new Ds\Set();

$startKeys = [];

while ($queue->count()) {
    [$x, $y, $steps] = $queue->shift();

    foreach ($directions as $delta) {
        [$mX, $mY] = [$x + $delta[0], $y + $delta[1]];
        if ($set->contains([$mX, $mY])) continue;

        $move = $map[$mY][$mX] ?? '';
        if ($move === '.') {
            $queue->push([$mX, $mY, $steps + 1]);
        } elseif (ctype_lower($move)) {
            $startKeys[$move] = [$mX, $mY, $steps + 1];
        }
    }

    $set->add([$x, $y]);
}

$totalMin = PHP_INT_MAX;

foreach ($startKeys as $key => [$x, $y, $steps]) {
    $min = $steps + findPath($keys, $key, new \Ds\Set([strtoupper($key)]), $steps);

    if ($min < $totalMin) {
        $totalMin = $min;
    }
}

echo 'Part 1: ' . $totalMin . PHP_EOL;


$map = [];
$robots = [];
$keys = [];
$replaceX = $replaceY = 0;

foreach ($input as $y => $item) {
    $map[$y] = str_split($item);
    foreach ($map[$y] as $x => $value) {
        if ($value === '@') {
            $robots = [
                [$x - 1, $y - 1, []], // Top left
                [$x - 1, $y + 1, []], // Bottom left
                [$x + 1, $y - 1, []], // Top right
                [$x + 1, $y + 1, []], // Bottom right
            ];
            $replaceX = $x;
            $replaceY = $y;
        } elseif (ctype_lower($value)) {
            $keys[$value] = [$x, $y];
        }
    }
}

$map[$replaceY][$replaceX] = '#';
$map[$replaceY-1][$replaceX] = '#';
$map[$replaceY+1][$replaceX] = '#';
$map[$replaceY][$replaceX-1] = '#';
$map[$replaceY][$replaceX+1] = '#';

$keys = mapKeyPaths($map, $keys);

// Supply robot with keys from other quadrants
foreach ($map as $y => $line) {
    foreach ($line as $x => $value) {
        if (ctype_lower($value)) {
            if ($x < $replaceX) {
                if ($y < $replaceY) {
                    $robots[1][2][] = $value;
                    $robots[2][2][] = $value;
                    $robots[3][2][] = $value;
                } else {
                    $robots[0][2][] = $value;
                    $robots[2][2][] = $value;
                    $robots[3][2][] = $value;
                }
            } else {
                if ($y < $replaceY) {
                    $robots[0][2][] = $value;
                    $robots[1][2][] = $value;
                    $robots[3][2][] = $value;
                } else {
                    $robots[0][2][] = $value;
                    $robots[1][2][] = $value;
                    $robots[2][2][] = $value;
                }
            }
        }
    }
}

$totalRobots = 0;
$startKeys = [];

// Get all possible starting positions
foreach ($robots as $index => [$myX, $myY, $robotKeys]) {
    $queue = new Ds\Deque();
    $queue->push([$myX, $myY, 0]);

    $set = new Ds\Set();

    $startKeys[$index] = [];

    while ($queue->count()) {
        [$x, $y, $steps] = $queue->shift();

        foreach ($directions as $delta) {
            [$mX, $mY] = [$x + $delta[0], $y + $delta[1]];
            if ($set->contains([$mX, $mY])) {
                continue;
            }

            $move = $map[$mY][$mX] ?? '';
            if ($move === '.') {
                $queue->push([$mX, $mY, $steps + 1]);
            } elseif (ctype_lower($move)) {
                $startKeys[$index][$move] = [$mX, $mY, $steps + 1];
            }
        }

        $set->add([$x, $y]);
    }

    $totalMin = PHP_INT_MAX;
    $memory = [];

    foreach ($startKeys[$index] as $key => [$x, $y, $steps]) {
        $hasKeys = new \Ds\Set([strtoupper($key)]);
        foreach ($robotKeys as $robotKey) {
            $hasKeys->add(strtoupper($robotKey));
        }

        $min = $steps + findPath($keys, $key, $hasKeys, $steps);

        if ($min < $totalMin) {
            $totalMin = $min;
        }
    }

    $totalRobots += $totalMin;
}

echo 'Part 2: ' . $totalRobots . PHP_EOL;

function findPath($keys, $start, \Ds\Set $collected, $steps) {
    global $memory;

    if (isset($memory[$start.$collected->sorted()->join()])) {
        return $memory[$start.$collected->sorted()->join()];
    }

    [$x, $y, $nextKeys] = $keys[$start];

    $hasNext = false;

    $min = PHP_INT_MAX;

    foreach ($nextKeys as $key => [$nX, $nY, $nSteps, $doors, $moreKeys]) {
        $ukey = strtoupper($key);
        if ($collected->contains($ukey) || !$collected->contains(...$doors) || !$collected->contains(...$moreKeys)) continue;
        $hasNext = true;
        $total = $nSteps + findPath($keys, $key, $collected->merge([...$moreKeys, $ukey]), $steps + $nSteps);

        if ($total < $min) {
            $min = $total;
        }
    }

    if (!$hasNext) {
        return 0;
    }

    $memory[$start.$collected->sorted()->join()] = $min;
    return $min;
}

// Calculate the distances and doors from each key to the others
function mapKeyPaths(array $map, array $keys): array
{
    global $directions;

    foreach ($keys as $key => [$x, $y]) {
        $queue = new \Ds\Deque();
        $queue->push([$x, $y, 0, [], []]);
        $set = new \Ds\Set();
        $nextKeys = [];

        while ($queue->count()) {
            [$x, $y, $steps, $doors, $moreKeys] = $queue->shift();

            foreach ($directions as $delta) {
                [$mX, $mY] = [$x + $delta[0], $y + $delta[1]];
                if ($set->contains([$mX, $mY])) continue;

                $move = $map[$mY][$mX] ?? '';
                if ($move === '.') {
                    $queue->push([$mX, $mY, $steps + 1, $doors, $moreKeys]);
                } elseif (ctype_lower($move)) {
                    if (!isset($nextKeys[$move])) {
                        $nextKeys[$move] = [$mX, $mY, $steps + 1, $doors, $moreKeys];
                    }
                    $queue->push([$mX, $mY, $steps + 1, $doors, array_merge($moreKeys, [strtoupper($move)])]);
                } elseif (ctype_upper($move)) {
                    $queue->push([$mX, $mY, $steps + 1, array_merge($doors, [$move]), $moreKeys]);
                }
            }

            $set->add([$x, $y]);
        }

        $keys[$key][] = $nextKeys;
    }

    return $keys;
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
