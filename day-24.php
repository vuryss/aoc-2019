<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-24'));
$start = microtime(true);
$input = explode("\n", $input);
$input = array_map(fn($row) => str_split($row), $input);
$originalInput = $input;

$minutes = 0;
$mem = [];

while (true) {
    $minutes++;

    $newInput = [];

    foreach ($input as $y => $line) {
        foreach ($line as $x => $item) {
            $num = 0;

            if (($input[$y][$x - 1] ?? '.') === '#') $num++;
            if (($input[$y + 1][$x] ?? '.') === '#') $num++;
            if (($input[$y][$x + 1] ?? '.') === '#') $num++;
            if (($input[$y - 1][$x] ?? '.') === '#') $num++;

            if ($item === '#' && $num != 1) {
                $newInput[$y][$x] = '.';
                continue;
            }

            if ($item === '.' && ($num === 1 || $num === 2)) {
                $newInput[$y][$x] = '#';
                continue;
            }

            $newInput[$y][$x] = $item;
        }
    }

    $input = $newInput;
    $compressed = serialize($input);
    if (isset($mem[$compressed])) {
        $sum = 0;

        foreach ($input as $y => $line) {
            foreach ($line as $x => $item) {
                if ($item === '#') {
                    $sum += 2 ** ($y * 5 + $x);
                }
            }
        }

        echo 'Part 1: ' . $sum . PHP_EOL;
        break;
    }

    $mem[$compressed] = $minutes;
}

$input = $originalInput;

$minutes = 0;
$input = [0 => $input];
$minLevel = 0;
$maxLevel = 0;

while (true) {
    $minutes++;

    $newInput = [];

    for ($level = $minLevel - 1; $level <= $maxLevel + 1; $level++) {
        $nextLevel = $level + 1;
        $prevLevel = $level - 1;

        for ($y = 0; $y < 5; $y++) {
            for ($x = 0; $x < 5; $x++) {
                $item = $input[$level][$y][$x] ?? '.';

                if ($y === 2 && $x === 2) continue;

                $num = 0;

                if ($y === 1 && $x === 2) {
                    if (($input[$nextLevel][0][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][0][1] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][0][2] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][0][3] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][0][4] ?? '.') === '#') $num++;
                }

                if ($y === 2 && $x === 1) {
                    if (($input[$nextLevel][0][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][1][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][2][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][3][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][0] ?? '.') === '#') $num++;
                }

                if ($y === 2 && $x === 3) {
                    if (($input[$nextLevel][0][4] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][1][4] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][2][4] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][3][4] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][4] ?? '.') === '#') $num++;
                }

                if ($y === 3 && $x === 2) {
                    if (($input[$nextLevel][4][0] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][1] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][2] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][3] ?? '.') === '#') $num++;
                    if (($input[$nextLevel][4][4] ?? '.') === '#') $num++;
                }

                if ($y === 0) {
                    if (($input[$prevLevel][1][2] ?? '.') === '#') $num++;
                }

                if ($x === 0) {
                    if (($input[$prevLevel][2][1] ?? '.') === '#') $num++;
                }

                if ($x === 4) {
                    if (($input[$prevLevel][2][3] ?? '.') === '#') $num++;
                }

                if ($y === 4) {
                    if (($input[$prevLevel][3][2] ?? '.') === '#') $num++;
                }

                if (($input[$level][$y][$x - 1] ?? '.') === '#') $num++;
                if (($input[$level][$y + 1][$x] ?? '.') === '#') $num++;
                if (($input[$level][$y][$x + 1] ?? '.') === '#') $num++;
                if (($input[$level][$y - 1][$x] ?? '.') === '#') $num++;

                if ($item === '#' && $num != 1) {
                    $newInput[$level][$y][$x] = '.';
                    continue;
                }

                if ($item === '.' && ($num === 1 || $num === 2)) {
                    $newInput[$level][$y][$x] = '#';
                    if ($minLevel === $level) {
                        $minLevel--;
                    }

                    if ($maxLevel === $level) {
                        $maxLevel++;
                    }
                    continue;
                }

                $newInput[$level][$y][$x] = $item;
            }
        }
    }

    $input = $newInput;

    if ($minutes === 200) {
        $bugs = 0;
        for ($level = $minLevel; $level <= $maxLevel; $level++) {
            for ($y = 0; $y < 5; $y++) {
                for ($x = 0; $x < 5; $x++) {
                    $item = $input[$level][$y][$x] ?? '.';
                    if ($item === '#') $bugs++;
                }
            }
        }

        echo 'Part 2: ' . $bugs . PHP_EOL;

        break;
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
