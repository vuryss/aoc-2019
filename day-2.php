<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-2'));
$input = explode(",", $input);
$input = array_map('intval', $input);
$start = microtime(true);

$part1 = parse($input, 12, 2);

for ($i = 0; $i < 100; $i++) {
    for ($j = 0; $j < 100; $j++) {
        if (parse($input, $i, $j) === 19690720) {
            $part2 = 100 * $i + $j;
            break;
        }
    }
}

function parse(array $program, int $noun, int $verb): int {
    $pos = 0;
    [$program[1], $program[2]] = [$noun, $verb];

    while ($code = $program[$pos++]) {
        if ($code === 99) {
            return (int) $program[0];
        }

        $value1 = $program[$program[$pos++]];
        $value2 = $program[$program[$pos++]];
        $program[$program[$pos++]] = $code === 1 ? $value1 + $value2 : $value1 * $value2;
    }

    return -1;
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
