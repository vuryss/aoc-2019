<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-5'));
$input = explode(",", $input);
$start = microtime(true);

echo 'Part 1: ' . compute($input, 1) . PHP_EOL;
echo 'Part 2: ' . compute($input, 5) . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;

function compute($prg, $input) {
    $pos = 0;
    $output = '';
    $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1, 5 => 2, 6 => 2, 7 => 3, 8 => 3, 99 => 0];
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
                $prg[$arg[1]] = $input;
                break;
            case 4:
                $output .= $arg[1];
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
                return ltrim($output, '0');
        }

        $pos += $numArgs[$opcode] + 1;
    }

    return '-error-';
}
