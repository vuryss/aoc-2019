<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-9'));
$input = explode(',', $input);
$start = microtime(true);

echo 'Part 1: ' . solve($input, 1) . PHP_EOL;
echo 'Part 2: ' . solve($input, 2) . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;

function solve($prg, $input) {
    $pos = $relPos = 0;
    $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1,  5 => 2,  6 => 2,  7 => 3, 8 => 3, 9 => 1, 99 => 0];
    $posArgs = [1 => 3, 2 => 3, 3 => 1, 4 => -1, 5 => -1, 6 => -1, 7 => 3, 8 => 3, 9 => -1];
    $output = [];

    while (true) {
        $a = str_pad((string) $prg[$pos], 5, '0', STR_PAD_LEFT);
        $opcode = (int) ($a[3] . $a[4]);
        $mode = [1 => (int) $a[2], 2 => (int) $a[1], 3 => (int) $a[0]];
        $arg = [];

        for ($i = 1; $i <= $numArgs[$opcode]; $i++) {
            $value = (int) $prg[$pos + $i];
            if ($posArgs[$opcode] === $i) {
                $arg[$i] = $mode[$i] === 2 ? $relPos + $value : $value;
            } elseif ($mode[$i] === 0) {
                $arg[$i] = (int) ($prg[$value] ?? 0);
            } elseif ($mode[$i] === 1) {
                $arg[$i] = $value;
            } else {
                $arg[$i] = (int) $prg[$relPos + $value];
            }
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
                $output[] = $arg[1];
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

            case 9:
                $relPos += $arg[1];
                break;

            case 99:
                return implode(',', $output);
        }

        $pos += $numArgs[$opcode] + 1;
    }
}
