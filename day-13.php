<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-13'));
$input = explode(',', $input);
$start = microtime(true);

$input[0] = 2;
$comp     = new IntCodeParser($input);
$grid     = [];
$started  = false;
$bX       = $pX = $score = $blocks = 0;

while (true) {
    $output1 = $comp->output();

    if ($output1 === null) break;

    $output2 = $comp->output();
    $output3 = $comp->output();

    if ($output1 === -1) {
        $score = $output3;
        $started = true;
        continue;
    }

    if ($started) {
        if ($output3 == 3) {
            $pX = $output1;
        } elseif ($output3 == 4) {
            $comp->input($output1 <=> $pX);
        }
    } elseif ($output3 === 2) {
        $blocks++;
    }
}

echo 'Part 1: ' . $blocks . PHP_EOL;
echo 'Part 2: ' . $score . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
