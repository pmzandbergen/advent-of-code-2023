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
$symbolPositions = [];
$lines = explode(PHP_EOL, $data);
foreach ($lines as $key => $line) {
    $symbolPositions[$key] = [];
    if (!preg_match_all('/[^0-9.]/', $line, $matches, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    foreach ($matches[0] as $match) {
        $symbolPositions[$key][] = $match[1];
    }
}
$partNumbers = [];
foreach ($lines as $key => $line) {
    if (!preg_match_all('/[0-9]+/', $line, $matches, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    $linePartNumbers = [];
    foreach ($matches[0] as $match) {
        $searchStart = $match[1] - 1;
        $searchEnd = $match[1] + strlen($match[0]);
        $searchPositions = array_merge(
            $symbolPositions[$key - 1] ?? [],
            $symbolPositions[$key],
            $symbolPositions[$key + 1] ?? []
        );
        $isPartNumber = false;
        foreach ($searchPositions as $searchPosition) {
            if ($searchPosition >= $searchStart && $searchPosition <= $searchEnd) {
                $isPartNumber = true;
                break;
            }
        }
        if (!$isPartNumber) {
            continue;
        }
        $linePartNumbers[] = $match[0];
    }
    $partNumbers = array_merge($partNumbers, $linePartNumbers);
    if (!$verbose) {
        continue;
    }
    echo 'Engine schematic line #' . ($key + 1) . ' (' . $line . ') has part numbers: ' . implode(', ', $linePartNumbers) . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all the part numbers is: ' . array_sum($partNumbers) . PHP_EOL;
