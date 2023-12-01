#!php
<?php
const NUMBERS = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9];
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Usage: ' . $argv[0] . ' calibration_document_file [-d|--detect-words] [-v|--verbose]' . PHP_EOL . PHP_EOL . 'Options' .
        PHP_EOL . ' -d, --detect-words Detect digits as words (one, two, etc.)' .
        PHP_EOL . ' -v, --verbose      Verbose output';
    echo 'Please provide the filename of your calibration document as first argument.' . PHP_EOL;
    exit(1);
}
$detectLetters = in_array('-d', $argv) || in_array('--detect-words', $argv);
$verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
$data = file_get_contents($path);
$regexGroup = $detectLetters ? '([0-9]|' . implode('|', array_keys(NUMBERS)) . ')' : '([0-9])';
$sum = 0;
foreach (explode(PHP_EOL, $data) as $lineNr => $line) {
    if (!preg_match('/' . $regexGroup . '/m', $line, $firstDigit)) {
        continue;
    }
    preg_match('/.*' . $regexGroup . '/m', $line, $secondDigit);
    $value = intval((NUMBERS[$firstDigit[1]] ?? $firstDigit[1]) . (NUMBERS[$secondDigit[1]] ?? $secondDigit[1]));
    $sum += $value;
    if (!$verbose) {
        continue;
    }
    echo 'Calibration value for line #' . ($lineNr + 1) . ' (' . $line . ') is: ' . $value . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all of the calibration values is: ' . $sum . PHP_EOL;
