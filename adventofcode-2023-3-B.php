#!php
<?php
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Usage: ' . $argv[0] . ' engine_schematic [-v|--verbose]' . PHP_EOL . PHP_EOL . 'Options' .
        PHP_EOL . ' -v, --verbose      Verbose output';
    echo 'Please provide the filename of your engine schematic as first argument.' . PHP_EOL;
    exit(1);
}
$verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
$data = file_get_contents($path);
$sum = 0;
$gears = [];
$lines = explode(PHP_EOL, $data);
foreach ($lines as $key => $line) {
    $gears[$key] = [];
    if (!preg_match_all('/\*/', $line, $matches, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    foreach ($matches[0] as $match) {
        $gears[$key][$match[1]] = [];
    }
}
foreach ($lines as $key => $line) {
    if (!preg_match_all('/[0-9]+/', $line, $matches, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    foreach ($matches[0] as $match) {
        $searchStart = $match[1] - 1;
        $searchEnd = $match[1] + strlen($match[0]);
        foreach ([$key - 1, $key, $key + 1] as $gearKey) {
            foreach (array_keys($gears[$gearKey] ?? []) as $gearPosition) {
                if ($gearPosition >= $searchStart && $gearPosition <= $searchEnd) {
                    $gears[$gearKey][$gearPosition][] = $match[0];
                }
            }
        }
    }
}
$gearRatiosSum = 0;
foreach ($lines as $key => $line) {
    if ($verbose) {
        $gearRatios = [];
        echo 'Engine schematic line #' . ($key + 1) . ' (' . $line . ') has gear ratios: ';
    }
    foreach ($gears[$key] as $position => $numbers) {
        if (count($numbers) !== 2) {
            continue;
        }
        $gearRatio = array_product($numbers);
        $gearRatiosSum += $gearRatio;
        if (!$verbose) {
            continue;
        }
        $gearRatios[] = $gearRatio . ' (at ' . $position . ')';
    }
    if (!$verbose) {
        continue;
    }
    echo implode(', ', $gearRatios) . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all the gear ratios is: ' . $gearRatiosSum . PHP_EOL;
