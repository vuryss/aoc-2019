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

$max = 0;

foreach (combinations([0,1,2,3,4]) as $comb) {
    $setting = str_split($comb);
    $a = new Amplifier($input, $setting[0]);
    $a->pushSignal(0);
    $inpSignal = $a->amplify()->current();

    $a = new Amplifier($input, $setting[1]);
    $a->pushSignal($inpSignal);
    $inpSignal = $a->amplify()->current();

    $a = new Amplifier($input, $setting[2]);
    $a->pushSignal($inpSignal);
    $inpSignal = $a->amplify()->current();

    $a = new Amplifier($input, $setting[3]);
    $a->pushSignal($inpSignal);
    $inpSignal = $a->amplify()->current();

    $a = new Amplifier($input, $setting[4]);
    $a->pushSignal($inpSignal);
    $result = $a->amplify()->current();

    if ($result > $max) $max = $result;
}

echo 'Part 1: ' . $max . PHP_EOL;

class Amplifier
{
    private $program = [];
    private $phase;
    private $input = [];

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

    public function amplify() {
        $prg = $this->program;
        $pos = 0;
        $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1, 5 => 2, 6 => 2, 7 => 3, 8 => 3, 99 => 0];
        $posArgs = [1 => 3, 2 => 3, 3 => 1, 4 => -1, 5 => -1, 6 => -1, 7 => 3, 8 => 3];

        while (true) {
            $a = str_pad((string) $prg[$pos], 5, '0', STR_PAD_LEFT);
            $opcode = (int) ($a[3] . $a[4]);
            $mode = [1 => (int) $a[2], 2 => (int) $a[1], 3 => (int) $a[0]];
            $arg = [];

            for ($i = 1; $i <= $numArgs[$opcode]; $i++) {
                $arg[$i] = $mode[$i] || $posArgs[$opcode] === $i ? (int) $prg[$pos + $i] : (int) $prg[$prg[$pos + $i]];
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
                    yield $arg[1];
                    break;
                case 5:
                    if ($arg[1] !== 0) {
                        $pos = $arg[2] - ($numArgs[$opcode] + 1);
                        break;
                    }
                    break;
                case 6:
                    if ($arg[1] === 0) {
                        $pos = $arg[2] - ($numArgs[$opcode] + 1);
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

            $pos += $numArgs[$opcode] + 1;
        }
    }
}

$results = [];

foreach (combinations([5,6,7,8,9]) as $comb) {
    $phase = str_split($comb);
    $a1    = new Amplifier($input, $phase[0]);
    $a2    = new Amplifier($input, $phase[1]);
    $a3    = new Amplifier($input, $phase[2]);
    $a4    = new Amplifier($input, $phase[3]);
    $a5    = new Amplifier($input, $phase[4]);

    $signal = 0;

    try {
        $a1->pushSignal($signal);
        $generator1 = $a1->amplify();
        $signal     = $generator1->current();

        $a2->pushSignal($signal);
        $generator2 = $a2->amplify();
        $signal     = $generator2->current();

        $a3->pushSignal($signal);
        $generator3 = $a3->amplify();
        $signal     = $generator3->current();

        $a4->pushSignal($signal);
        $generator4 = $a4->amplify();
        $signal     = $generator4->current();

        $a5->pushSignal($signal);
        $generator5 = $a5->amplify();
        $signal     = $generator5->current();

        while (true) {
            $a1->pushSignal($signal);
            $generator1->next();;
            $signal = $generator1->current();

            $a2->pushSignal($signal);
            $generator2->next();
            $signal = $generator2->current();

            $a3->pushSignal($signal);
            $generator3->next();
            $signal = $generator3->current();

            $a4->pushSignal($signal);
            $generator4->next();
            $signal = $generator4->current();

            $a5->pushSignal($signal);
            $generator5->next();
            $signal = $generator5->current();
        }
    } catch (Exception $e) {
        $results[] = $signal;
    }
}

echo 'Part 2: ' . max($results) . PHP_EOL;

echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;
