const fs = require('fs');
const input = fs
    .readFileSync(__dirname + '/input/day-3', 'utf-8')
    .trim()
    .split("\n");

const start = new Date().getTime();

let paths = [];
let dx = {'L': -1, 'R': 1, 'D': 0, 'U':  0};
let dy = {'L':  0, 'R': 0, 'D': 1, 'U': -1};

input.forEach((wire, index) => {
    wire = wire.split(',');
    let x = 0, y = 0, s = 0;
    paths[index] = [];

    wire.forEach(move => {
        for (let i = 0, steps = parseInt(move.substr(1)); i < steps; i++) {
            x += dx[move[0]];
            y += dy[move[0]];
            paths[index][y + '.' + x] = ++s;
        }
    });
});

let intersections = Object.keys(paths[0]).filter({}.hasOwnProperty.bind(paths[1]));
let sums1 = [], sums2 = [];

intersections.forEach(coords => {
    [x, y] = coords.split('.');
    sums1.push(Math.abs(x) + Math.abs(y));
    sums2.push(paths[0][coords] + paths[1][coords]);
});

console.log('Part 1: ' + Math.min(...sums1));
console.log('Part 2: ' + Math.min(...sums2));

console.log('Finished in: ' + (new Date().getTime() - start) + 'ms');
