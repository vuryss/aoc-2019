<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-11'));
$input = explode(',', $input);
$start = microtime(true);

$parseBlack = robot($input, 0);
$part1 = count($parseBlack, COUNT_RECURSIVE) - count($parseBlack);

$grid = robot($input, 1);

$allX = $allY = [];

foreach ($grid as $x => $yLine) {
    $allX[] = $x;
    foreach ($yLine as $y => $dummy) {
        $allY[] = $y;
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . PHP_EOL;
for ($y = max($allY), $minY = min($allY); $y >= $minY; $y--) {
    for ($x = min($allX), $maxX = max($allX); $x <= $maxX; $x++) {
        echo $grid[$x][$y] ?? 0 == 1 ? '#' : ' ';
    }
    echo PHP_EOL;
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;

function robot($intCode, $startColor)
{
    $program = new IntCodeParser($intCode);
    $dirMap = ['^' => [0 => '<', 1 => '>'], '>' => [0 => '^', 1 => 'v'], '<' => [0 => 'v', 1 => '^'], 'v' => [0 => '>', 1 => '<']];
    $deltaX = ['^' => 0, '>' => 1, 'v' => 0, '<' => -1];
    $deltaY = ['^' => 1, '>' => 0, 'v' => -1, '<' => 0];
    $grid = [0 => [0 => $startColor]];
    $x = $y = 0;
    $dir = '^';

    while (true) {
        $program->input($grid[$x][$y] ?? 0);

        $paint = $program->output();

        if ($paint === null) {
            return $grid;
        }

        $grid[$x][$y] = $paint;
        $dir          = $dirMap[$dir][$program->output()];
        $x            += $deltaX[$dir];
        $y            += $deltaY[$dir];
    }
}
