<?php
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Please provide the filename of your calibration document as first argument.' . PHP_EOL;
    exit(1);
}
$data = file_get_contents($path);
preg_match_all('/^[^0-9]*([0-9]).*$/m', $data, $matchesA);
preg_match_all('/([0-9])[^0-9]*$/m', $data, $matchesB);
$sum = 0;
foreach (array_keys($matchesA[1]) as $key) {
    $value = intval($matchesA[1][$key] . $matchesB[1][$key]);
    $sum += $value;
    echo 'Calibration value for line ' . ($key + 1) . ' (' . $matchesA[0][$key] . ') is: ' . $value . PHP_EOL;
}
echo '------------------------' . PHP_EOL . 'The sum of all of the calibration values is: ' . $sum . PHP_EOL;
