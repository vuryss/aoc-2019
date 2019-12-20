<?php

$input = rtrim(file_get_contents(__DIR__ . '/input/day-20'), "\n");
$input = explode("\n", $input);
$start = microtime(true);

$map = [];
$minX = $minY = 2;
$maxX = $maxY = 0;
$positions = [];
$teleport = [];

foreach ($input as $y => $line) {
    $line = str_split($line);
    foreach ($line as $x => $value) {
        if ($value === '.' || $value === '#') {
            $map[$y][$x] = $value;
            if ($x > $maxX) $maxX = $x;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

foreach ($input as $y => $line) {
    $line = str_split($line);
    foreach ($line as $x => $value) {
        if (ctype_upper($value)) {
            // Upper side portals
            if ($y === 0) {
                $label = $value . $input[$y + 1][$x];
                $positions[$label]['OUT'] = [$x, 2];
            }

            // Left side
            if ($x === 0) {
                $label = $value . $input[$y][$x + 1];
                $positions[$label]['OUT'] = [2, $y];
            }

            // Right side
            if ($x === $maxX + 2) {
                $label = $input[$y][$x - 1] . $value;
                $positions[$label]['OUT'] = [$x - 2, $y];
            }

            // Bottom && inner top
            if ($y === $maxY + 1) {
                $label = $value . $input[$y + 1][$x];
                $positions[$label]['OUT'] = [$x, $y - 1];
            } elseif ($y > 5 && $input[$y - 1][$x] === '.') {
                $label = $value . $input[$y + 1][$x];
                $positions[$label]['IN'] = [$x, $y - 1];
            }

            // Inner bottom
            if ($y > 5 && ($input[$y + 1][$x] ?? '') === '.') {
                $label = $input[$y - 1][$x] . $value;
                $positions[$label]['IN'] = [$x, $y + 1];
            }

            // Inner left
            if ($x > 0 && $x < $maxX && $input[$y][$x - 1] === '.') {
                $label = $value . $input[$y][$x + 1];
                $positions[$label]['IN'] = [$x - 1, $y];
            }

            // Inner right
            if ($x > 2 && ($input[$y][$x + 1] ?? '') === '.') {
                $label = $input[$y][$x - 1] . $value;
                $positions[$label]['IN'] = [$x + 1, $y];
            }
        }
    }
}

foreach ($positions as $label => $gates) {
    if (count($gates) !== 2) continue;

    $teleport[$gates['IN'][0]][$gates['IN'][1]]   = ['IN', $gates['OUT'], $label];
    $teleport[$gates['OUT'][0]][$gates['OUT'][1]] = ['OUT', $gates['IN'], $label];
}

$startPos = $positions['AA']['OUT'];
$endPos = $positions['ZZ']['OUT'];

// Down, Up, Right, Left
$directions = [[0, 1], [0, -1], [1, 0], [-1, 0]];

// Part 1
$queue = new \Ds\Queue();
$queue->push([...$startPos, 0]);
$visited = new \Ds\Set();

while ($queue->count()) {
    [$x, $y, $steps] = $queue->pop();

    foreach ($directions as $direction) {
        $mX = $x + $direction[0];
        $mY = $y + $direction[1];

        if ($visited->contains([$mX, $mY])) continue;

        if (($map[$mY][$mX] ?? '') === '.') {
            if ($mX === $endPos[0] && $mY === $endPos[1]) {
                echo 'Part 1: ' . ($steps + 1) . PHP_EOL;
                break 2;
            }

            if (isset($teleport[$mX][$mY])) {
                $dest = $teleport[$mX][$mY];
                $queue->push([$dest[1][0], $dest[1][1], $steps + 2]);
            } else {
                $queue->push([$mX, $mY, $steps + 1]);
            }
        }
    }

    $visited->add([$x, $y]);
}

// Part 2
$queue = new \Ds\Queue();
$queue->push([...$startPos, 0, 0]);
$visited = [new \Ds\Set()];

while ($queue->count()) {
    [$x, $y, $steps, $level] = $queue->pop();

    foreach ($directions as $direction) {
        $mX = $x + $direction[0];
        $mY = $y + $direction[1];

        if (isset($visited[$level]) && $visited[$level]->contains([$mX, $mY])) continue;

        if (($map[$mY][$mX] ?? '') === '.') {
            if ($level === 0 && $mX === $endPos[0] && $mY === $endPos[1]) {
                echo 'Part 2: ' . ($steps + 1) . PHP_EOL;
                break 2;
            }

            if (isset($teleport[$mX][$mY])) {
                $dest = $teleport[$mX][$mY];
                if (!($dest[0] === 'OUT' && $level === 0)) {
                    $newLevel = $level + (($dest[0] === 'OUT') ? -1 : 1);
                    $queue->push([$dest[1][0], $dest[1][1], $steps + 2, $newLevel]);
                }
            } else {
                $queue->push([$mX, $mY, $steps + 1, $level]);
            }
        }
    }

    $visited[$level] ??= new \Ds\Set();
    $visited[$level]->add([$x, $y]);
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
