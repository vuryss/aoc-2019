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

class IntCodeParser
{
    private $program = [];
    private $input   = [];
    private $pos     = 0;
    private $relPos  = 0;

    public function __construct(array $program, ...$input)
    {
        $this->program = $program;
        array_push($this->input, ...$input);
    }

    public function input(int $input)
    {
        $this->input[] = $input;
    }

    public function output() {
        $prg = &$this->program;
        $numArgs = [1 => 3, 2 => 3, 3 => 1, 4 => 1,  5 => 2,  6 => 2,  7 => 3, 8 => 3, 9 => 1, 99 => 0];
        $posArgs = [1 => 3, 2 => 3, 3 => 1, 4 => -1, 5 => -1, 6 => -1, 7 => 3, 8 => 3, 9 => -1];

        while (true) {
            $a = str_pad((string) $prg[$this->pos], 5, '0', STR_PAD_LEFT);
            $opcode = (int) ($a[3] . $a[4]);
            $mode = [1 => (int) $a[2], 2 => (int) $a[1], 3 => (int) $a[0]];
            $arg = [];

            for ($i = 1; $i <= $numArgs[$opcode]; $i++) {
                $value = (int) $prg[$this->pos + $i];
                if ($posArgs[$opcode] === $i) {
                    $arg[$i] = $mode[$i] === 2 ? $this->relPos + $value : $value;
                } elseif ($mode[$i] === 0) {
                    $arg[$i] = (int) ($prg[$value] ?? 0);
                } elseif ($mode[$i] === 1) {
                    $arg[$i] = $value;
                } else {
                    $arg[$i] = (int) $prg[$this->relPos + $value];
                }
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
                    return (int) $arg[1];
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

                case 9:
                    $this->relPos += $arg[1];
                    break;

                case 99:
                    return null;
            }

            $this->pos += $numArgs[$opcode] + 1;
        }
    }
}
