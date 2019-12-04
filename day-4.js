const fs = require('fs');
const input = fs
                .readFileSync(__dirname + '/input/day-4', 'utf-8')
                .trim()
                .split('-');
const start = new Date().getTime();

let part1 = 0, part2 = 0;

for (let num = +input[0], max = +input[1]; num <= max; num++) {
    let sNum = num.toString();
    let chars = sNum.split('');
    let p1 = p2 = false;

    if (sNum !== chars.sort().join('')) {
        continue;
    }

    let cCount = {};

    for (let i in chars) {
        cCount[chars[i]] = cCount[chars[i]] ? cCount[chars[i]] + 1 : 1;
    }

    for (let char in cCount) {
        if (cCount[char] === 2) p1 = p2 = true;
        else if (cCount[char] > 2) p1 = true;
    }

    if (p1) part1++;
    if (p2) part2++;
}

console.log('Part 1: ' + part1);
console.log('Part 2: ' + part2);

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
