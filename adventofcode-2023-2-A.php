#!php
<?php
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Usage: ' . $argv[0] . ' games_file [-v|--verbose]' . PHP_EOL . PHP_EOL . 'Options' .
        PHP_EOL . ' -v, --verbose      Verbose output';
    echo 'Please provide the filename of your game document as first argument.' . PHP_EOL;
    exit(1);
}
$verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
$data = file_get_contents($path);
$sum = 0;
$maxCubes = [
    'red' => 12,
    'green' => 13,
    'blue' => 14,
];
foreach (explode(PHP_EOL, $data) as $line) {
    if (!preg_match('/^Game ([0-9]+):/', $line, $matches)) {
        continue;
    }
    $game = $matches[1];
    $possible = true;
    $sets = explode('; ', explode(': ', $line)[1]);
    foreach ($sets as $set) {
        $cubes = [
            'red' => preg_match('/([0-9]+) red/', $set, $matches) ? (int) $matches[1] : 0,
            'green' => preg_match('/([0-9]+) green/', $set, $matches) ? (int) $matches[1] : 0,
            'blue' => preg_match('/([0-9]+) blue/', $set, $matches) ? (int) $matches[1] : 0,
        ];
        foreach ($maxCubes as $color => $max) {
            if ($cubes[$color] <= $max) {
                continue;
            }
            $possible = false;
            break 2;
        }
    }
    if ($possible) {
        $sum += $game;
    }
    if (!$verbose) {
        continue;
    }
    echo 'Game #' . $game . ' is ' . ($possible ? '' : 'im') . 'possible ' . '(' .  implode('; ', $sets) . ')' . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all games that are possible is: ' . $sum . PHP_EOL;
