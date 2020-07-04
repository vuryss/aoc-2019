<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-23'));
$input = explode(',', $input);
$start = microtime(true);

/** @var IntCodeParser[] $comp */
$comp = [];
$queue = [];

for ($i = 0; $i < 50; $i++) {
    $queue[$i] = [];
    $comp[$i]  = new IntCodeParser($input, $i);
}

$part1 = false;

$idleCounter = 0;
$mem = $yList = [];

while (true) {
    if (++$idleCounter > 1) {
        $queue[0] = $mem;

        if (isset($yList[$mem[1]])) {
            echo 'Part 2: ' . $mem[1] . PHP_EOL;
            break;
        }

        $yList[$mem[1]] = true;
    }

    for ($i = 0; $i < 50; $i++) {
        if (!empty($queue[$i])) {
            $comp[$i]->input(array_shift($queue[$i]));
            $comp[$i]->input(array_shift($queue[$i]));
        }

        if ($target = $comp[$i]->output()) {
            $idleCounter = 0;
            $X = $comp[$i]->output();
            $Y = $comp[$i]->output();
            $queue[$target][] = $X;
            $queue[$target][] = $Y;

            if ($target === 255) {
                if (!$part1) {
                    echo 'Part 1: ' . $Y . PHP_EOL;
                    $part1 = true;
                }

                $mem = [$X, $Y];
            }
        }
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
