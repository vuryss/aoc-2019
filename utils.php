<?php

function permutations(array $input): Generator {
    if (count($input) === 1) {
        yield current($input);
        return;
    }

    foreach ($input as $key => $value) {
        $copy = $input;
        unset($copy[$key]);

        foreach (permutations($copy) as $subCombination) {
            yield $value . $subCombination;
        }
    }
}
