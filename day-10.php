<?php

//$input = trim(file_get_contents(__DIR__ . '/input/day-10'));
$input = '#...##.####.#.......#.##..##.#.
#.##.#..#..#...##..##.##.#.....
#..#####.#......#..#....#.###.#
...#.#.#...#..#.....#..#..#.#..
.#.....##..#...#..#.#...##.....
##.....#..........##..#......##
.##..##.#.#....##..##.......#..
#.##.##....###..#...##...##....
##.#.#............##..#...##..#
###..##.###.....#.##...####....
...##..#...##...##..#.#..#...#.
..#.#.##.#.#.#####.#....####.#.
#......###.##....#...#...#...##
.....#...#.#.#.#....#...#......
#..#.#.#..#....#..#...#..#..##.
#.....#..##.....#...###..#..#.#
.....####.#..#...##..#..#..#..#
..#.....#.#........#.#.##..####
.#.....##..#.##.....#...###....
###.###....#..#..#.....#####...
#..##.##..##.#.#....#.#......#.
.#....#.##..#.#.#.......##.....
##.##...#...#....###.#....#....
.....#.######.#.#..#..#.#.....#
.#..#.##.#....#.##..#.#...##..#
.##.###..#..#..#.###...#####.#.
#...#...........#.....#.......#
#....##.#.#..##...#..####...#..
#.####......#####.....#.##..#..
.#...#....#...##..##.#.#......#
#..###.....##.#.......#.##...##';
//$input = '.#..##.###...#######
//##.############..##.
//.#.######.########.#
//.###.#######.####.#.
//#####.##.#.##.###.##
//..#####..#.#########
//####################
//#.####....###.#.#.##
//##.#################
//#####.##.###..####..
//..######..##.#######
//####.##.####...##..#
//.#####..#.######.###
//##...#.##########...
//#.##########.#######
//.####.#.###.###.#.##
//....##.##.###..#####
//.#.#.###########.###
//#.#.#.#####.####.###
//###.##.####.##.#..##';
//$input = '......#.#.
//#..#.#....
//..#######.
//.#.#.###..
//.#..#.....
//..#....#.#
//#..#....#.
//.##.#..###
//##...#..#.
//.#....####';
//$input = '.#..#
//.....
//#####
//....#
//...##';
$input = explode("\n", $input);
$start = microtime(true);

$map = [];
$maxY = count($input);
$maxX = strlen($input[0]);

foreach ($input as $y => $line) {
    $line = str_split($line);
    foreach ($line as $x => $item) {
        if ($item === '#') {
            $map[$x][$y] = $item;
        }
    }
}

//detectOthers($map, 1, 2);

$positions = [];

foreach ($map as $x => $yLine) {
    foreach ($yLine as $y => $item) {
        $positions[$x . '.' . $y] = detectOthers($map, $x, $y);
        //echo 'Position ' . $x . ',' . $y . ' detects: ' . detectOthers($map, $x, $y) . PHP_EOL;
        //break 2;
    }
}

$max = max($positions);
$maxPos = array_search($max, $positions);
[$foundX, $foundY] = explode('.', $maxPos);


echo 'Max: ' . max($positions) . PHP_EOL;
echo 'Position: ' . $maxPos . PHP_EOL;

killOthers($map, $foundX, $foundY);

function killOthers($map, $x, $y) {
    $lines = [];

    foreach ($map as $x1 => $yLine) {
        foreach ($yLine as $y1 => $item) {
            if ($x1 === $x && $y1 === $y) {
                continue;
            }

            $lines[] = [($x1 - $x), ($y1 - $y)];
        }
    }

    $map = [];

    foreach ($lines as $key => $line) {
        if ($line[0] >= 0) {
            if ($line[1] > 0) {
                $angle = bcdiv($line[0], $line[1], 3);
                // < 90
            } elseif ($line[1] === 0) {
                $angle = M_PI_2;
            } else {
                if ($line[0] === 0) {
                    // = 180
                    $angle = M_PI;
                } else {
                    // > 90 < 180
                    $angle = M_PI_2 + bcdiv(abs($line[1]), $line[0], 3);
                }
            }
        } else {
            if ($line[1] < 0) {
                $angle = M_PI + bcdiv(abs($line[0]), abs($line[1]), 3);
                // > 180 < 270
            } elseif ($line[1] === 0) {
                $angle = M_PI + M_PI_2;
            } else {
                $angle = M_PI + M_PI_2 + bcdiv($line[1], abs($line[0]), 3);
                // > 270 < 360
            }
        }

        echo 'Angle: ' . $angle . PHP_EOL;
    }
}

function detectOthers($map, $x, $y)
{
    $lines = [];

    foreach ($map as $x1 => $yLine) {
        foreach ($yLine as $y1 => $item) {
            if ($x1 === $x && $y1 === $y) {
                continue;
            }

            $lines[] = [($x1 - $x), ($y1 - $y)];
        }
    }

    $checked = [];

    $change = true;
    while ($change) {
        $change = false;

        foreach ($lines as $key => $line) {
            if (isset($checked[$key])) continue;
            $checked[$key] = true;

            foreach ($lines as $key2 => $line2) {
                if ($key === $key2) continue;

                if (($line[0] < 0 && $line2[0] > 0)
                    || ($line[0] > 0 && $line2[0] < 0)
                    || ($line[1] < 0 && $line2[1] > 0)
                    || ($line[1] > 0 && $line2[1] < 0)
                ) {
                    continue;
                }

                if ($line[0] === 0 && $line2[0] === 0 || $line[1] === 0 && $line2[1] === 0) {
                    unset($lines[$key2]);
                    $change = true;
                    continue;
                }

                if ($line2[0] === 0 || $line2[1] === 0 || $line[0] === 0 || $line[1] === 0) {
                    continue;
                }

                $a1 = bcdiv($line[0], $line[1], 3);
                $a2 = bcdiv($line2[0], $line2[1], 3);

                if ($a1 === $a2) {
                    unset($lines[$key2]);
                    $change = true;
                    continue;
                }
            }

            if ($change) {
                break;
            }
        }
    }

    echo count($lines) . PHP_EOL;

    return count($lines);
}


echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
