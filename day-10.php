<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-10'));
$input = explode("\n", $input);
$start = microtime(true);

$part1 = $part2 = 0;

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

detectOthers($map, 1, 2);

$positions = [];

foreach ($map as $x => $yLine) {
    foreach ($yLine as $y => $item) {
        $positions[$x . '.' . $y] = detectOthers($map, $x, $y);
    }
}

$part1 = max($positions);
$maxPos = array_search($part1, $positions);
[$foundX, $foundY] = explode('.', $maxPos);

$part2 = killOthers($map, $foundX, $foundY);

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

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
            if ($line[1] < 0) {
                $angle = atan(bcdiv($line[0], abs($line[1]), 3));
                // < 90
            } elseif ($line[1] === 0) {
                $angle = M_PI_2;
            } else {
                if ($line[0] === 0) {
                    // = 180
                    $angle = M_PI;
                } else {
                    // > 90 < 180
                    $angle = M_PI_2 + atan(bcdiv($line[1], $line[0], 3));
                }
            }
        } else {
            if ($line[1] > 0) {
                $angle = M_PI + atan(bcdiv(abs($line[0]), $line[1], 3));
                // > 180 < 270
            } elseif ($line[1] === 0) {
                $angle = M_PI + M_PI_2;
            } else {
                $angle = M_PI + M_PI_2 + atan(bcdiv(abs($line[1]), abs($line[0]), 3));
                // > 270 < 360
            }
        }

        $dist = abs($line[0]) + abs($line[1]);

        $map[(string) $angle][$dist] = [$line[0] + $x, $line[1] + $y];
    }

    ksort($map);

    foreach ($map as $rad => $items) {
        ksort($items);
        $map[$rad] = $items;
    }

    $counter = 0;

    while (true) {
        foreach ($map as $rad => $items) {
            $counter++;
            $item = array_shift($items);
            if ($counter === 200) {
                return 100 * $item[0] + $item[1];
            }
            if (empty($items)) {
                unset($map[$rad]);
            }
        }
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

    return count($lines);
}


echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
