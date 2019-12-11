<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-5'));
$input = explode(",", $input);
$start = microtime(true);

$comp = new IntCodeParser($input, 1);

echo 'Part 1: ';
while (($num = $comp->output()) !== null) echo $num ?: '';
echo PHP_EOL;

$comp = new IntCodeParser($input, 5);

echo 'Part 2: ';
while ($num = $comp->output()) echo $num;
echo PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
