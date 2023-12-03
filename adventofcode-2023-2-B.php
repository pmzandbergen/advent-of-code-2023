#!php
<?php
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Usage: ' . $argv[0] . ' games_file [-v|--verbose]' . PHP_EOL . PHP_EOL . 'Options' .
        PHP_EOL . ' -v, --verbose      Verbose output';
    echo 'Please provide the filename of your calibration document as first argument.' . PHP_EOL;
    exit(1);
}
$verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
$data = file_get_contents($path);
$sum = 0;
foreach (explode(PHP_EOL, $data) as $line) {
    if (!preg_match('/^Game ([0-9]+):/', $line, $matches)) {
        continue;
    }
    $game = $matches[1];
    $possible = true;
    $sets = explode('; ', explode(': ', $line)[1]);
    $minCubes = ['red' => 0, 'green' => 0, 'blue' => 0];
    foreach ($sets as $set) {
        $cubes = [
            'red' => preg_match('/([0-9]+) red/', $set, $matches) ? (int) $matches[1] : 0,
            'green' => preg_match('/([0-9]+) green/', $set, $matches) ? (int) $matches[1] : 0,
            'blue' => preg_match('/([0-9]+) blue/', $set, $matches) ? (int) $matches[1] : 0,
        ];
        foreach ($cubes as $color => $cnt) {
            $minCubes[$color] = $cnt < $minCubes[$color] ? $minCubes[$color] : $cnt;
        }
    }
    $power = $minCubes['red'] * $minCubes['green'] * $minCubes['blue'];
    $sum = $sum + $power;
    if (!$verbose) {
        continue;
    }
    echo 'Game #' . $game . ' requires the following number of cubes: ' . implode('; ', [
        $minCubes['red'] . ' red',
        $minCubes['green'] . ' green',
        $minCubes['blue'] . ' blue',
    ]) . '. The power of those numbers is: ' . $power . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all powers is: ' . $sum . PHP_EOL;
