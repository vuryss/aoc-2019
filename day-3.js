const fs = require('fs');
const input = fs
    .readFileSync(__dirname + '/input/day-3', 'utf-8')
    .trim()
    .split("\n");

const start = new Date().getTime();

wires = paths = [];

input.forEach((wire, index) => {
    wire = wire.split(',');
    x = y = s = 0;
    paths[index] = [];

    wire.forEach((move) => {
        steps = parseInt(move.substr(1));

        switch (move[0]) {
            case 'R':
                for (i = 0; i < steps; i++) {
                    paths[index][y + '.' + ++x] = ++s;
                }
                break;
            case 'L':
                for (i = 0; i < steps; i++) {
                    paths[index][y + '.' + --x] = ++s;
                }
                break;
            case 'D':
                for (i = 0; i < steps; i++) {
                    paths[index][++y + '.' + x] = ++s;
                }
                break;
            case 'U':
                for (i = 0; i < steps; i++) {
                    paths[index][--y + '.' + x] = ++s;
                }
                break;
        }
    });
});

intersections = Object.keys(paths[0]).filter({}.hasOwnProperty.bind(paths[1]));
sums1 = [];
sums2 = [];

intersections.forEach(coords => {
    [x, y] = coords.split('.');
    sums1.push(Math.abs(x) + Math.abs(y));
    sums2.push(paths[0][coords] + paths[1][coords]);
});

console.log('Part 1: ' + Math.min(...sums1));
console.log('Part 2: ' + Math.min(...sums2));

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
