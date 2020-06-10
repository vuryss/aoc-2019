<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-22'));;
$input = explode("\n", $input);

const NUM_CARDS = 10007;
const SEARCH_CARD = 2019;

$cards = range(0, NUM_CARDS - 1);

$start = microtime(true);

foreach ($input as $action) {
    $parts = explode(' ', $action);

    if (strpos($action, 'deal into') === 0) {
        $cards = array_reverse($cards);
        continue;
    }

    if (strpos($action, 'deal with') === 0) {
        $deck = [];
        $i = 0;
        $num = (int) $parts[3];

        foreach ($cards as $card) {
            $deck[$i] = $card;
            $i += $num;
            $i %= NUM_CARDS;
        }

        $cards = $deck;
        ksort($cards);
        continue;
    }

    if (strpos($action, 'cut') === 0) {
        $num = (int) $parts[1];

        if ($num >= 0) {
            $cards = array_merge(array_slice($cards, $num), array_slice($cards, 0, $num));
        } else {
            $cards = array_merge(array_slice($cards, $num), array_slice($cards, 0, NUM_CARDS + $num));
        }
        continue;
    }
}

echo 'Part 1: ' . array_search(SEARCH_CARD, $cards) . PHP_EOL;

$input = array_reverse($input);

const NUM_CARDS_2 = 119315717514047;
const SEARCH_CARD_2 = 2020;

$searchPos   = SEARCH_CARD_2;

$fns = [
    'stack'     => fn($total, $position, $arg): int => $total - $position - 1,
    'increment' => fn($total, $position, $arg): int => $position * $arg,
    'cut'       => fn($total, $position, $arg): int => $total + $position - $arg,
];

// Construct the formula
// $a * $pos + $b
$a = 1;
$b = 0;

foreach ($input as $action) {
    $parts = explode(' ', $action);

    switch (true) {
        case $parts[1] === 'into':
            $a = gmp_mul($a, -1);
            $b = gmp_add(gmp_mul($b, -1), NUM_CARDS_2 - 1);
            break;

        case $parts[1] === 'with':
            $z = gmp_intval(gmp_invert($parts[3], NUM_CARDS_2));
            $a = gmp_mul($a, $z);
            $b = gmp_mul($b, $z);
            break;

        case $parts[0] === 'cut':
            $b = gmp_add($b, NUM_CARDS_2 + (int) $parts[1]);
            break;
    }

    $a = gmp_mod($a, NUM_CARDS_2);
    $b = gmp_mod($b, NUM_CARDS_2);
}

echo 'Formula: ' . gmp_strval($a) . '*x + ' . gmp_strval($b) . PHP_EOL;

$b = gmp_mul($b, gmp_powm(gmp_sub(1, $a), NUM_CARDS_2 - 2, NUM_CARDS_2));
$a = gmp_powm($a, 101741582076661, NUM_CARDS_2);

$searchPos = gmp_strval(gmp_mod(gmp_add(gmp_mul($a, $searchPos), gmp_mul(gmp_sub(1, $a), $b)), NUM_CARDS_2));

echo 'Part 2: ' . $searchPos . PHP_EOL;
echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
