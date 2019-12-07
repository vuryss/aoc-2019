<?php

$input = trim(file_get_contents(__DIR__ . '/input/day-7'));
$input = explode(',', $input);
$start = microtime(true);

function combinations($input) {
    if (count($input) === 1) {
        yield current($input);
        return;
    }

    foreach ($input as $key => $value) {
        $copy = $input;
        unset($copy[$key]);

        foreach (combinations($copy) as $subCombination) {
            yield $value . $subCombination;
        }
    }
}

$part1 = 0;

foreach (combinations([0,1,2,3,4]) as $comb) {
    $setting = str_split($comb);
    $signal = 0;
    for ($i = 0; $i < 5; $i++) {
        $signal = (new Amplifier($input, $setting[$i]))->amplify($signal);
    }

    if ($signal > $part1) $part1 = $signal;
}

$part2 = 0;

foreach (combinations([5,6,7,8,9]) as $comb) {
    $phase = str_split($comb);
    $a1    = new Amplifier($input, $phase[0]);
    $a2    = new Amplifier($input, $phase[1]);
    $a3    = new Amplifier($input, $phase[2]);
    $a4    = new Amplifier($input, $phase[3]);
    $a5    = new Amplifier($input, $phase[4]);

    $signal = 0;

    try {
        while (true) {
            $signal = $a5->amplify($a4->amplify($a3->amplify($a2->amplify($a1->amplify($signal)))));
        }
    } catch (Exception $e) {
        if ($signal > $part2) $part2 = $signal;
    }
}

echo 'Part 1: ' . $part1 . PHP_EOL;
echo 'Part 2: ' . $part2 . PHP_EOL;

class Amplifier
{
    private $program = [];
    private $phase;
    private $input   = [];
    private $pos     = 0;

    public function __construct(array $program, int $phase)
    {
        $this->program = $program;
        $this->phase   = $phase;
        $this->input[] = $this->phase;
    }

    public function pushSignal(int $signal)
    {
        $this->input[] = $signal;
    }

    public function amplify(?int $signal = null) {
        if (isset($signal)) $this->pushSignal($signal);
        $prg = $this->program;
        $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1, 5 => 2, 6 => 2, 7 => 3, 8 => 3, 99 => 0];
        $posArgs = [1 => 3, 2 => 3, 3 => 1, 4 => -1, 5 => -1, 6 => -1, 7 => 3, 8 => 3];

        while (true) {
            $a = str_pad((string) $prg[$this->pos], 5, '0', STR_PAD_LEFT);
            $opcode = (int) ($a[3] . $a[4]);
            $mode = [1 => (int) $a[2], 2 => (int) $a[1], 3 => (int) $a[0]];
            $arg = [];

            for ($i = 1; $i <= $numArgs[$opcode]; $i++) {
                $arg[$i] = $mode[$i] || $posArgs[$opcode] === $i
                        ? (int) $prg[$this->pos + $i]
                        : (int) $prg[$prg[$this->pos + $i]];
            }

            switch ($opcode) {
                case 1:
                    $prg[$arg[3]] = (int) $arg[1] + (int) $arg[2];
                    break;
                case 2:
                    $prg[$arg[3]] = (int) $arg[1] * (int) $arg[2];
                    break;
                case 3:
                    $prg[$arg[1]] = array_shift($this->input);
                    break;
                case 4:
                    $this->pos += $numArgs[$opcode] + 1;
                    return $arg[1];
                case 5:
                    if ($arg[1] !== 0) {
                        $this->pos = $arg[2] - ($numArgs[$opcode] + 1);
                        break;
                    }
                    break;
                case 6:
                    if ($arg[1] === 0) {
                        $this->pos = $arg[2] - ($numArgs[$opcode] + 1);
                    }
                    break;
                case 7:
                    $prg[$arg[3]] = $arg[1] < $arg[2] ? 1 : 0;
                    break;
                case 8:
                    $prg[$arg[3]] = $arg[1] === $arg[2] ? 1 : 0;
                    break;

                case 99:
                    throw new Exception();
            }

            $this->pos += $numArgs[$opcode] + 1;
        }
    }
}

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
