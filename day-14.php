<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-14'));
$input = explode("\n", $input);
$start = microtime(true);

$map = [];

foreach ($input as $item) {
    preg_match_all('/\d+\s\w+/', $item, $matches);
    $product = explode(' ', array_pop($matches[0]));
    $map[$product[1]] = [$product[0] => []];
    foreach ($matches[0] as $a) {
        $b = explode(' ', $a);
        $map[$product[1]][$product[0]][$b[1]] = $b[0];
    }
}

$target = 1000000000000;
$guessFuel = $half = 10000000;
$guessOre = getNeededOreForFuel($map, $guessFuel);

do {
    $half = floor($half / 2);
    $guessFuel += $guessOre > $target ? -$half : $half;
    $guessOre = getNeededOreForFuel($map, $guessFuel);
} while ($half > 1);

echo 'Part 1: ' . getNeededOreForFuel($map, 1) . PHP_EOL;
echo 'Part 2: ' . $guessFuel . PHP_EOL;
echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;

function getMapForFuelCount(array $map, int $fuelCount) {
    $newMap = $map;
    unset($newMap['FUEL'][1]);

    $fuel = $map['FUEL'][1];

    foreach ($fuel as $mat => $quantity) {
        $newMap['FUEL'][$fuelCount][$mat] = $quantity * $fuelCount;
    }

    return $newMap;
}

function getNeededOreForFuel($map, int $fuelCount)
{
    $map = getMapForFuelCount($map, $fuelCount);
    $extra = [];
    $neededOre = 0;
    $need = $map['FUEL'][$fuelCount];

    while (count($need)) {
        $after = [];

        foreach ($need as $mat => $quantity) {
            $a = $map[$mat];
            $b = key($a);
            if ($extra[$mat] ?? 0) {
                if ($extra[$mat] > $quantity) {
                    $extra[$mat] -= $quantity;
                    continue;
                } else {
                    $quantity -= $extra[$mat];
                    $extra[$mat] = 0;
                }
            }
            foreach ($a[$b] as $mat2 => $quantity2) {
                $needQ = ceil($quantity / $b)*$quantity2;
                $extraQ = $needQ / $quantity2 * $b - $quantity;
                $after[$mat2] = $after[$mat2] ?? 0;
                $after[$mat2] += $needQ;
            }

            $extraQ = $needQ / $quantity2 * $b - $quantity;
            if ($extraQ) {
                $extra[$mat] = ($extra[$mat] ?? 0) + $extraQ;
            }

        }

        $neededOre += $after['ORE'] ?? 0;
        unset($after['ORE']);

        $need = $after;
    }

    return $neededOre;
}
