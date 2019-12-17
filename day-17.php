<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-17'));
$input = explode(',', $input);
$start = microtime(true);

$comp = new IntCodeParser($input);

$map = [];
$y = $x = $rX = $rY = 0;
$maxX = $maxY = 0;

while (true) {
    if (!$output = $comp->output()) break;

    if ($output === 10) {
        $x = 0;
        $y++;
        continue;
    }

    if ($output === 94) {
        $rX = $x;
        $rY = $y;
    }

    $map[$y][$x++] = chr($output);

    if ($x > $maxX) $maxX = $x;
}

$maxY = $y;
$sum = 0;

for ($y = 0; $y <= $maxY; $y++) {
    for ($x = 0; $x <= $maxX; $x++) {
        if (($map[$y][$x] ?? '') === '#'
            && ($map[$y+1][$x] ?? '') === '#' && ($map[$y-1][$x] ?? '') === '#'
            && ($map[$y][$x+1] ?? '') === '#' && ($map[$y][$x-1] ?? '') === '#'
        ) {
            $sum += $x * $y;
        }
    }
}

echo 'Part 1: ' . $sum . PHP_EOL;

$dir = 'U';

$turns = ['U' => ['L', 'R'], 'L' => ['D', 'U'], 'R' => ['U', 'D'], 'D' => ['R', 'L']];
$deltaX = ['U' => 0, 'L' => -1, 'R' => 1, 'D' => 0];
$deltaY = ['U' => -1, 'L' => 0, 'R' => 0, 'D' => 1];

$steps = [];
$moves = 0;
$lastTurn = '';

while (true) {
    $next = $map[$rY + $deltaY[$dir]][$rX + $deltaX[$dir]] ?? '';
    if ($next === '#') {
        $moves++;
        $rY += $deltaY[$dir];
        $rX += $deltaX[$dir];
        continue;
    }

    foreach ($turns[$dir] as $turn => $newDir) {
        $next = $map[$rY + $deltaY[$newDir]][$rX + $deltaX[$newDir]] ?? '';
        if ($next === '#') {
            if ($moves) $steps[] = $lastTurn . $moves;
            $lastTurn = $turn ? 'R' : 'L';
            $dir   = $newDir;
            $moves = 1;
            $rY    += $deltaY[$dir];
            $rX    += $deltaX[$dir];
            continue 2;
        }
    }

    $steps[] = $lastTurn . $moves;
    break;
}

function combineIntoThree($steps, $level = 1, $found = [])
{
    $string = implode(',', $steps);
    $stepsCount = count($steps);

    $subSteps = [];

    foreach ($steps as $step) {
        $subStepsCount = count($subSteps) + 1;
        if ($step === '0') {
            if ($subStepsCount === 2) break;
            $subSteps = [];
            continue;
        }
        $subSteps[] = $step;

        if ($subStepsCount < 2) continue;

        $substr2 = implode(',', $subSteps);
        if (substr_count($string, $substr2) === 1) break;

        $stepsCopy = $steps;
        for ($i = 0, $to = $stepsCount - $subStepsCount; $i <= $to; $i++) {
            foreach ($subSteps as $j => $subStep) {
                if ($steps[$i + $j] !== $subStep) continue 2;
            }

            foreach ($subSteps as $j => $subStep) {
                $stepsCopy[$i + $j] = '0';
            }

            $i += $subStepsCount -1;
        }

        if ($level < 3) {
            $foundCopy = $found;
            $foundCopy[] = $substr2;

            if ($result = combineIntoThree($stepsCopy, $level + 1, $foundCopy)) {
                return $result;
            }
        } elseif ($level === 3) {
            if (!count(array_diff($stepsCopy, ['0']))) {
                $found[] = $substr2;
                return $found;
            }
        }
    }

    return null;
}

$subCombinations = combineIntoThree($steps);

$routine = implode(',', $steps);
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
        if ($char === 'L' || $char === 'R') $comp->input(ord(','));
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
