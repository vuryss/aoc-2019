<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-19'));
$input = explode(',', $input);
$start = microtime(true);

$count = 0;
$map = [];

$fromX = 0;
for ($y = 0; $y < 50; $y++) {
    $found = false;
    for ($x = $fromX; $x <= $y; $x++) {
        $comp = new IntCodeParser($input);
        $comp->input($x);
        $comp->input($y);

        if ($comp->output() === 1) {
            $count++;
            if (!$found) {
                $fromX = $x;
                $found = true;
            }
            $map[$y][$x] = '#';
        } elseif ($found) {
            break;
        }
    }
}

echo 'Part 1: ' . $count . PHP_EOL;

$value = 2000;
$half = $value;
$result = isFit($value);
$checked = [$value => true];

while (true) {
    $half = ceil($half / 2);
    $value += $result ? -$half : $half;

    if (isset($checked[$value])) {
        echo 'Part 2: ' . ((isFit($value) * 10000) + $value) . PHP_EOL;
        break;
    }

    $result = isFit($value);
    $checked[$value] = true;
}

function isFit(int $value) {
    global $input;

    $width = 0;
    $foundBeam = false;

    for ($x = 0; $x < $value; $x++) {
        $comp = new IntCodeParser($input);
        $comp->input($x);
        $comp->input($value);
        if ($comp->output() === 1) {
            $foundBeam = true;
            $width++;
        } elseif ($foundBeam) {
            break;
        }
    }

    if ($width < 120) return false;
    $x -= 100;

    for ($y = $value, $to = $y + 100; $y < $to; $y++) {
        $comp = new IntCodeParser($input);
        $comp->input($x);
        $comp->input($y);

        if ($comp->output() !== 1) {
            return false;
        }
    }

    return $x;
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
