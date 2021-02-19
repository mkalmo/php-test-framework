<?php

$files = scandir(__DIR__);

foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    } else if (strpos(__FILE__, $file) !== false) {
        continue;
    }

//    if ($file !== 'nav-tests.php') {
//        continue;
//    }

    $cmd = sprintf('php %s', $file);

    $output = [];

    exec($cmd, $output);

    $outputString = implode("\n", $output);

    $result = areAllPassed($outputString) ? ' OK' : " NOK";

    printf("%s%s\n", $file, $result);
}

function areAllPassed(string $output) : bool {
    preg_match("/(\d+) of (\d+) tests passed./", $output, $matches);

    return count($matches) && $matches[1] == $matches[2];
}

