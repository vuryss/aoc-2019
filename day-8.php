<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-8'));
$start = microtime(true);

$width          = 25;
$height         = 6;
$strLen         = strlen($input);
$numLayers      = $strLen / ($width * $height);
$digitsPerLayer = $strLen / $numLayers;
$map            = [];
$image          = [];

for ($i = 0; $i < $numLayers; $i++) {
    $chunk = substr($input, $i * $digitsPerLayer, $digitsPerLayer);
    $chars = count_chars($chunk, 1);
    $map[$i] = $chars[48] ?? 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            if (($image[$y][$x] ?? 2) !== 2) continue;
            $image[$y][$x] = (int) $chunk[$y * $width + $x];
        }
    }
}

$layerWithMinZero = array_search(min($map), $map);
$layer = substr($input, $layerWithMinZero * $digitsPerLayer, $digitsPerLayer);
$chars = count_chars($layer, 1);

echo 'Part 1: ' . ($chars[49] * $chars[50]) . PHP_EOL;
echo 'Part 2: ' . PHP_EOL;

foreach ($image as $y => $line) {
    foreach ($line as $x => $pixel) {
        echo $pixel == 1 ? '#' : ' ';
    }
    echo PHP_EOL;
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
