<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-7'));
$input = explode(',', $input);
$start = microtime(true);

$part1 = 0;

foreach (permutations([0,1,2,3,4]) as $comb) {
    $setting = str_split($comb);
    $signal = 0;
    for ($i = 0; $i < 5; $i++) {
        $gen = amplify($input, $setting[$i]);
        $gen->current();
        $signal = $gen->send($signal);
    }

    if ($signal > $part1) $part1 = $signal;
}

$part2 = 0;

foreach (permutations([5,6,7,8,9]) as $comb) {
    $phase = str_split($comb);
    $i = 0;
    $signal = 0;
    $amps = [amplify($input, $phase[0]), amplify($input, $phase[1]), amplify($input, $phase[2]), amplify($input, $phase[3]), amplify($input, $phase[4])];

    for ($i = 0; $i < 5; $i++) {
        $amps[$i]->current();
        $signal = $amps[$i]->send($signal);
    }

    while (true) {
        for ($i = 0; $i < 5; $i++) {
            $amps[$i]->next();
            if (!$amps[$i]->valid()) {
                if ($signal > $part2) $part2 = $signal;
                break 2;
            }
            $signal = $amps[$i]->send($signal);
        }
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

function amplify(array $prg, int $phase)
{
    $input = [$phase];
    $pos = 0;
    $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1,  5 => 2,  6 => 2,  7 => 3, 8 => 3, 99 => 0];
    $posArgs = [1 => 3, 2 => 3, 3 => 1, 4 => -1, 5 => -1, 6 => -1, 7 => 3, 8 => 3];

    while (true) {
        $a = str_pad((string) $prg[$pos], 5, '0', STR_PAD_LEFT);
        $opcode = (int) ($a[3] . $a[4]);
        $mode = [1 => (int) $a[2], 2 => (int) $a[1], 3 => (int) $a[0]];
        $arg = [];

        for ($i = 1; $i <= $numArgs[$opcode]; $i++) {
            $arg[$i] = $mode[$i] || $posArgs[$opcode] === $i ? (int) $prg[$pos + $i] : (int) $prg[$prg[$pos + $i]];
        }

        switch ($opcode) {
            case 1:
                $prg[$arg[3]] = (int) $arg[1] + (int) $arg[2];
                break;
            case 2:
                $prg[$arg[3]] = (int) $arg[1] * (int) $arg[2];
                break;
            case 3:
                if (empty($input)) {
                    $prg[$arg[1]] = yield;
                    break;
                }
                $prg[$arg[1]] = array_shift($input);
                break;
            case 4:
                yield $arg[1];
                break;
            case 5:
                if ($arg[1] !== 0) {
                    $pos = $arg[2] - ($numArgs[$opcode] + 1);
                    break;
                }
                break;
            case 6:
                if ($arg[1] === 0) {
                    $pos = $arg[2] - ($numArgs[$opcode] + 1);
                }
                break;
            case 7:
                $prg[$arg[3]] = $arg[1] < $arg[2] ? 1 : 0;
                break;
            case 8:
                $prg[$arg[3]] = $arg[1] === $arg[2] ? 1 : 0;
                break;

            case 99:
                return;
        }

        $pos += $numArgs[$opcode] + 1;
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
