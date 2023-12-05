#!php
<?php
$path = __DIR__ . '/' . ($argv[1] ?? '');
if (!is_file($path)) {
    echo 'Usage: ' . $argv[0] . ' scratchcards [-v|--verbose]' . PHP_EOL . PHP_EOL . 'Options' .
        PHP_EOL . ' -v, --verbose      Verbose output';
    echo 'Please provide the filename of your scratchcards as first argument.' . PHP_EOL;
    exit(1);
}
$verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
$data = file_get_contents($path);
$sum = 0;
foreach (explode(PHP_EOL, $data) as $line) {
    if (!preg_match('/^Card +([0-9]+): +([0-9 ]+) +\| +([0-9 ]+)$/', $line, $matches)) {
        continue;
    }
    $cardNr = $matches[1];
    $winningNumbers = preg_split('/ +/', $matches[2]);
    $cardNumbers = preg_split('/ +/', $matches[3]);
    $matches = [];
    foreach ($cardNumbers as $cardNumber) {
        if (!in_array($cardNumber, $winningNumbers, true)) {
            continue;
        }
        $matches[] = $cardNumber;
    }
    $score = empty($matches) ? 0 : pow(2, count($matches) - 1);
    $sum += $score;
    if (!$verbose) {
        continue;
    }
    echo 'Card #' . ($cardNr) . ' scores ' . $score . ' points with ' . count($matches) . ' matches (' . implode(', ', $matches) . ')' . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'The sum of all the points is: ' . $sum . PHP_EOL;
