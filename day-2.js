const fs = require('fs');
const input = fs
                .readFileSync(__dirname + '/input/day-2', 'utf-8')
                .trim()
                .split(",")
                .map(n => +n);

const start = new Date().getTime();

let part1 = parse(input.slice(), 12, 2), part2;

for (let i = 0; i < 100; i++) {
    for (let j = 0; j < 100; j++) {
        if (parse(input.slice(), i, j) === 19690720) {
            part2 = 100 * i + j;
            break;
        }
    }
}

function parse(program, noun, verb) {
    let pos = 0;
    program[1] = noun;
    program[2] = verb;

    while (true) {
        let code = program[pos++];
        if (code === 99) {
            return program[0];
        }

        let value1 = program[program[pos++]];
        let value2 = program[program[pos++]];
        program[program[pos++]] = code === 1 ? value1 + value2 : value1 * value2;
    }
}

console.log('Part 1: ' + part1);
console.log('Part 2: ' + part2);

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
