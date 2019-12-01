const fs = require('fs');
const input = fs.readFileSync(__dirname + '/input/day-1', 'utf-8').trim().split("\n");
const start = new Date().getTime();

let part1 = 0, part2 = 0;

input.forEach((item) => {
    let value = ~~(item / 3) - 2;
    part1 += value;

    while (value > 0) {
        part2 += value;
        value = ~~(value / 3) - 2;
    }
});

console.log('Part 1: ' + part1);
console.log('Part 2: ' + part2);

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
