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
$cardCnt = [];
foreach (explode(PHP_EOL, $data) as $line) {
    if (!preg_match('/^Card +([0-9]+): +([0-9 ]+) +\| +([0-9 ]+)$/', $line, $matches)) {
        continue;
    }
    $cardNr = $matches[1];
    $cardCnt[$cardNr] = 1;
}
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
    for ($extraCardNr = 1; $extraCardNr <= count($matches); $extraCardNr++) {
        $cardCnt[$cardNr + $extraCardNr] += $cardCnt[$cardNr];
    }
    $sum += $cardCnt[$cardNr];
    if (!$verbose) {
        continue;
    }
    echo 'You have a total of ' . $cardCnt[$cardNr] . ' of Card #' . $cardNr . PHP_EOL;
}
echo ($verbose ? '--------------------------------------------------' . PHP_EOL : '') .
    'Your total number of scratchcards: ' . $sum . PHP_EOL;
