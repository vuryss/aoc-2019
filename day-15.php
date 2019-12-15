<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-15'));
$input = explode(",", $input);
$start = microtime(true);

$comp = new IntCodeParser($input);

$map = [0 => [0 => '.']];
$deck = new Ds\Deque();
$deck->push([0, 0, 0, $comp]);
$visited = new \Ds\Set();
$foundX = $foundY = $numberOfSteps = 0;
$deltaX = [1 => 0, 0, -1, 1];
$deltaY = [1 => 1, -1, 0, 0];

while ($deck->count()) {
    [$x, $y, $steps, $comp] = $deck->shift();
    if ($visited->contains([$x, $y])) continue;
    $visited->add([$x, $y]);

    for ($i = 1; $i <= 4; $i++) {
        $c = clone $comp;
        $c->input($i);
        $o = $c->output();
        $newX = $x + $deltaX[$i];
        $newY = $y + $deltaY[$i];

        if ($o === 0) {
            $map[$newX][$newY] = '#';
        } elseif ($o === 1) {
            $map[$newX][$newY] = '.';
            $deck->push([$newX, $newY, $steps + 1, $c]);
        } else {
            $map[$newX][$newY] = 'X';
            $foundX = $newX;
            $foundY = $newY;
            $numberOfSteps = $steps + 1;
        }
    }
}

$deck = new \Ds\Deque();
$deck->push([$foundX, $foundY, 0]);

$maxSec = 0;

while ($deck->count()) {
    [$x, $y, $sec] = $deck->shift();
    if ($sec > $maxSec) $maxSec = $sec;

    for ($i = 1; $i <= 4; $i++) {
        $newX = $x + $deltaX[$i];
        $newY = $y + $deltaY[$i];

        if (($map[$newX][$newY] ?? '#') == '.') {
            $map[$newX][$newY] = 'O';
            $deck->push([$newX, $newY, $sec + 1]);
        }
    }
}

echo 'Part 1: ' . $numberOfSteps . PHP_EOL;
echo 'Part 2: ' . $maxSec . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
