<?php

require_once __DIR__ . '/utils.php';

$input = trim(file_get_contents(__DIR__ . '/input/day-25'));
$input = explode(',', $input);
$start = microtime(true);

$consoleInput = fopen('php://stdin', 'r');

$backMove = [
    'east' => 'west',
    'west' => 'east',
    'north' => 'south',
    'south' => 'north',
];

$gotAllItems = false;
$move = '';

$drone = new Drone(new IntCodeParser($input));
$drone->execute();

while (true) {
    $drone->pickWhiteListedItems();

    if ($drone->see( 'Pressure-Sensitive Floor')) {
        echo 'Part 1: ' . $drone->getDigits() . PHP_EOL;
        break;
    }

    if ($drone->see('Security Checkpoint') && !$gotAllItems) {
        $vertex->finished = true;
        $vertex->visited = true;
        $move = $backMove[$move];
        $vertex = $vertex->moves[$move];
        $drone->command($move);
        continue;
    }

    $vertex ??= new Vertex();
    $vertex->visited = true;

    foreach (['east', 'west', 'north', 'south'] as $move) {
        if ($drone->see($move . "\n")) {
            if (!isset($vertex->moves[$move])) {
                $vertex->moves[$move] = new Vertex();
                $vertex->moves[$move]->moves[$backMove[$move]] = $vertex;
            }
        }
    }

    if (count($vertex->moves) === 1) {
        $vertex->finished = true;
        $move = array_key_first($vertex->moves);
        $vertex = $vertex->moves[$move];
        $drone->command($move);
        continue;
    }

    foreach ($vertex->moves as $possibleMove => $targetVertex) {
        if ($targetVertex->finished) continue;

        if (!$targetVertex->visited) {
            $foundNew = true;
            $move = $possibleMove;
            $vertex = $targetVertex;
            $drone->command($move);
            continue 2;
        }
    }

    $unblocked = 0;

    foreach ($vertex->moves as $targetVertex) {
        if (!$targetVertex->finished) $unblocked++;
    }

    if ($unblocked === 1) {
        $vertex->finished = true;
    } elseif ($unblocked === 0) {
        $gotAllItems = true;
        $vertex = null;
        continue;
    }

    foreach ($vertex->moves as $possibleMove => $targetVertex) {
        if ($targetVertex->finished) continue;

        $move = $possibleMove;
        $vertex = $targetVertex;
        $drone->command($move);
        break;
    }
}

fclose($consoleInput);
echo 'Finished in ' . (microtime(true) - $start) . PHP_EOL;

class Drone
{
    private IntCodeParser $computer;
    private string $lastOutput;

    const WHITELIST_ITEMS = [
        'mug',
        'coin',
        'hypercube',
        'astrolabe',
    ];

    public function __construct(IntCodeParser $computer)
    {
        $this->computer = $computer;
    }

    public function command(string $command): void
    {
        foreach (str_split($command) as $char) {
            $this->computer->input(ord($char));
        }

        $this->computer->input(ord("\n"));

        $this->execute();
    }

    public function getDigits(): string
    {
        preg_match('/\d+/', $this->lastOutput, $matches);
        return $matches[0];
    }

    public function execute(): void
    {
        $this->lastOutput = '';

        while ($output = $this->computer->output()) {
            $this->lastOutput .= chr($output);
        }
    }

    public function see(string $something): bool
    {
        return strpos($this->lastOutput, $something) !== false;
    }

    public function pickWhiteListedItems()
    {
        foreach (self::WHITELIST_ITEMS as $item) {
            if (strpos($this->lastOutput, '- ' . $item)) {
                $this->command('take ' . $item);
                break;
            }
        }
    }
}

class Vertex
{
    public array $moves = [];
    public bool $finished = false;
    public bool $visited = false;
}
