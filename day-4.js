const fs = require('fs');
const input = fs
                .readFileSync(__dirname + '/input/day-4', 'utf-8')
                .trim()
                .split('-');
const start = new Date().getTime();

let part1 = 0, part2 = 0;

for (let num = +input[0], max = +input[1]; num <= max; num++) {
    let sNum = num.toString();

    if (sNum !== sNum.split('').sort().join('')) {
        continue;
    }

    if (sNum.match(/(?:(?:(\d?)(?!\1))|^)(\d)\2(?!\2)/)) {
        part1++;
        part2++;
    } else if (sNum.match(/(\d)\1/)) {
        part1++;
    }
}

console.log('Part 1: ' + part1);
console.log('Part 2: ' + part2);

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
