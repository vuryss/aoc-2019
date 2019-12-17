<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-17'));
$input = explode(',', $input);
$start = microtime(true);

$comp = new IntCodeParser($input);

$map = [];
$y = $x = 0;
$maxX = $maxY = 0;

while (true) {
    $output = $comp->output();

    if ($output === null) {
        break;
    }

    if ($output === 35) {
        $map[$y][$x++] = chr($output);
    } elseif ($output === 46) {
        $map[$y][$x++] = chr($output);
    } elseif ($output === 10) {
        $y++;
        $x = 0;
    } else {
        $map[$y][$x++] = chr($output);
    }

    if ($x > $maxX) $maxX = $x;
    if ($y > $maxY) $maxY = $y;
}

$sum = 0;
$rX = $rY = 0;
$dir = 'U';

for ($y = 0; $y <= $maxY; $y++) {
    for ($x = 0; $x <= $maxX; $x++) {

        if (($map[$y][$x] ?? '') === '#') {
            if (isset($map[$y+1][$x]) && $map[$y+1][$x] === '#'
                && isset($map[$y-1][$x]) && $map[$y-1][$x] === '#'
                && isset($map[$y][$x+1]) && $map[$y][$x+1] === '#'
                && isset($map[$y][$x-1]) && $map[$y][$x-1] === '#'
            ) {
                $sum += $x * $y;
            }
        }

        if (($map[$y][$x] ?? '') === '^') {
            $rX = $x;
            $rY = $y;
        }
    }
}

echo 'Part 1: ' . $sum . PHP_EOL;

$avail = [
    'U' => ['L', 'U', 'R'],
    'L' => ['D', 'L', 'U'],
    'R' => ['U', 'R', 'D'],
    'D' => ['R', 'D', 'L'],
];
$deltaX = [
    'U' => 0,
    'L' => -1,
    'R' => 1,
    'D' => 0,
];
$deltaY = [
    'U' => -1,
    'L' => 0,
    'R' => 0,
    'D' => 1,
];

$steps = [];

$moves = 0;
[$x, $y] = [$rX, $rY];

while (true) {
    // Check forward
    $next = $map[$y + $deltaY[$dir]][$x + $deltaX[$dir]] ?? '';
    if ($next === '#') {
        $moves++;
        $y += $deltaY[$dir];
        $x += $deltaX[$dir];
        continue;
    }

    // Check Left
    $left = $map[$y + $deltaY[$avail[$dir][0]]][$x + $deltaX[$avail[$dir][0]]] ?? '';
    if ($left === '#') {
        $steps[] = $moves;
        $steps[] = 'L';
        $dir   = $avail[$dir][0];
        $moves = 1;
        $y     += $deltaY[$dir];
        $x     += $deltaX[$dir];
        continue;
    }

    // Check Right
    $right = $map[$y + $deltaY[$avail[$dir][2]]][$x + $deltaX[$avail[$dir][2]]] ?? '';
    if ($right === '#') {
        $steps[] = $moves;
        $steps[] = 'R';
        $dir   = $avail[$dir][2];
        $moves = 1;
        $y     += $deltaY[$dir];
        $x     += $deltaX[$dir];
        continue;
    }

    $steps[] = $moves;
    break;
}

array_shift($steps);

function norm($steps) {
    $tuples = [];

    for ($i = 0, $to = count($steps); $i < $to; $i += 2) {
        $tuples[] = $steps[$i] . $steps[$i+1];
    }

    return $tuples;
}

function combineIntoThree($steps, $level = 1, $found = [])
{
    $string = implode(',', $steps);

    $subSteps = [];

    //echo '---------- LEVEL --------- ' . $level . ' ---------------- ' . PHP_EOL;

    foreach ($steps as $step) {
        if ($step === '0') {
            if (count($subSteps) === 1) break;
            $subSteps = [];
            continue;
        }
        $subSteps[] = $step;

        if (count($subSteps) < 2) continue;

        $substr2 = implode(',', $subSteps);
        $count = substr_count($string, $substr2);
        if ($count === 1) break;

        $copy = $steps;
        for ($i = 0; $i <= count($steps) - count($subSteps); $i++) {
            foreach ($subSteps as $j => $subStep) {
                if ($steps[$i + $j] !== $subStep) continue 2;
            }

            foreach ($subSteps as $j => $subStep) {
                $copy[$i + $j] = '0';
            }
        }


        //echo 'Substring: ' . $substr2 . ' counts: ' . $count . PHP_EOL;
        //echo 'Replaced string: ' . implode(',', $copy) . PHP_EOL;

        if ($level < 3) {
            $foundCopy = $found;
            $foundCopy[] = $substr2;
            $result = combineIntoThree($copy, $level + 1, $foundCopy);

            if ($result) return $result;
        } elseif ($level === 3) {
            if (strspn(implode(',', $copy), '0,') === strlen(implode(',', $copy))) {
                $found[] = $substr2;
                return $found;
            }
        }
    }

    return null;
}

$subCombinations = combineIntoThree(norm($steps));

$routine = implode(',', norm($steps));
foreach ($subCombinations as $index => $subCombination) {
    $routine = str_replace($subCombination, chr(ord('A') + $index), $routine);
}

$input[0] = 2;
$comp = new IntCodeParser($input);

foreach (str_split($routine) as $char) {
    $comp->input(ord($char));
}
$comp->input(10);

foreach ($subCombinations as $subCombination) {
    foreach (str_split($subCombination) as $char) {
        $comp->input(ord($char));
        if ($char === 'L' || $char === 'R') {
            $comp->input(ord(','));
        }
    }
    $comp->input(10);
}

$comp->input(ord('n'));
$comp->input(10);

while (true) {
    $output = $comp->output();

    if ($output === null) break;

    if ($output > 255) {
        echo 'Part 2: ' . $output . PHP_EOL;
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
