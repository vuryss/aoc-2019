<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-10'));
$input = explode("\n", $input);
$start = microtime(true);

$part1 = $part2 = 0;

$map = [];
$maxY = count($input);
$maxX = strlen($input[0]);

foreach ($input as $y => $line) {
    $line = str_split($line);
    foreach ($line as $x => $item) {
        if ($item === '#') {
            $map[$x][$y] = $item;
        }
    }
}

detectOthers($map, 1, 2);

$positions = [];

foreach ($map as $x => $yLine) {
    foreach ($yLine as $y => $item) {
        $positions[$x . '.' . $y] = detectOthers($map, $x, $y);
    }
}

$part1 = max($positions);
$maxPos = array_search($part1, $positions);
[$foundX, $foundY] = explode('.', $maxPos);

$part2 = killOthers($map, $foundX, $foundY);

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

function killOthers($map, $x, $y) {
    $angles = [];

    foreach ($map as $x1 => $yLine) {
        foreach ($yLine as $y1 => $item) {
            if ($x1 === $x && $y1 === $y) continue;

            $line = [($x1 - $x), ($y1 - $y)];
            $angle = atan2($line[1], $line[0]) + M_PI_2;
            $dist  = abs($line[0]) + abs($line[1]);

            if ($angle < 0) $angle += 2*M_PI;

            $angles[(string) $angle][$dist] = [$x1, $y1];
        }
    }

    ksort($angles);

    foreach ($angles as $rad => $items) {
        ksort($items);
        $angles[$rad] = $items;
    }

    $counter = 0;

    while (true) {
        foreach ($angles as $rad => $items) {
            $counter++;
            $item = array_shift($items);
            if ($counter === 200) {
                return 100 * $item[0] + $item[1];
            }
            if (empty($items)) {
                unset($angles[$rad]);
            }
        }
    }
}

function detectOthers($map, $x, $y)
{
    $angles = [];

    foreach ($map as $x1 => $yLine) {
        foreach ($yLine as $y1 => $item) {
            if ($x1 === $x && $y1 === $y) {
                continue;
            }

            $angles[(string) atan2($y - $y1, $x1 - $x)] = true;
        }
    }

    return count($angles);
}


echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
