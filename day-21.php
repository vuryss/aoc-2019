<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-21'));
$input = explode(',', $input);
$start = microtime(true);

// Part 1
$inst = "NOT A T\nNOT B J\nOR J T\nNOT C J\nOR J T\nNOT D J\nNOT J J\nAND T J\nWALK\n";
echo 'Part 1: ' . runInstr($input, $inst) . PHP_EOL;

// Part 2
$inst = "NOT A T\nNOT B J\nOR J T\nNOT C J\nOR J T\nNOT D J\nNOT J J\nAND T J\nNOT E T\nNOT T T\nOR H T\nAND T J\nRUN\n";
echo 'Part 2: ' . runInstr($input, $inst) . PHP_EOL;

function runInstr(array $input, string $instr) {
    $comp = new IntCodeParser($input);

    foreach (str_split($instr) as $char) {
        $comp->input(ord($char));
    }

    while ($output = $comp->output()) {
        if ($output > 255) return $output;
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
