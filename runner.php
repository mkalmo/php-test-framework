<?php

namespace stf;

require_once 'browser/parser/ParseException.php';

use \RuntimeException;
use tplLib\ParseException;

function runTests(?PointsReporter $reporter) {
    $total = 0;
    $successful = 0;

    foreach (getTestNames() as $testName) {
        if (!function_exists($testName)) {
            continue;
        }

        $total++;

        try {
            call_user_func($testName);

            $successful++;

            printf("%s() OK\n", $testName);

        } catch (ParseException $ex) {
            printf("### ERROR: %s ####\n", ERROR_W02);
            printf("Found incorrect HTML \n");
            printf("%s \n", $ex->getMessage());
            printf("Position %s \n", $ex->pos);

            printf("\n### Test %s() failed ###\n\n", $testName);
        } catch (FrameworkException $ex) {
            printf("### ERROR: %s ####\n", $ex->getCode());
            printf("%s \n", $ex->getMessage());
            printf("\n### Test %s() failed ###\n\n", $testName);
        } catch (RuntimeException $e) {
            printf("\n### Test %s() failed ###\n\n %s\n\n", $testName, $e);
        }
    }

    printf("%s of %s tests passed.\n", $successful, $total);

    if ($reporter) {
        $reporter->execute($successful);
    }
}

function getTestNames() : array {
    $testFilePath = get_included_files()[0];

    $contents = file_get_contents($testFilePath);

    $contents = preg_replace('/\s+/', ' ', $contents);
    preg_match_all('/(#Helper)? function \w+ ?\(/', $contents, $matches);

    if (!isset($matches[0])) {
        return [];
    }

    $testNames = [];
    foreach ($matches[0] as $match) {
        if (stripos($match, '#Helper') !== false) {
            continue;
        }

        preg_match('/function (\w+)/', $match, $nameMatch);

        $testNames[] = $nameMatch[1];
    }

    if (containsSelectedTests($testNames)) {
        $testNames = array_filter($testNames, function($name) {
            return startsWith($name, '_');
        });
    }

    return $testNames;
}

function containsSelectedTests($testNames) : bool {
    foreach ($testNames as $name) {
        if (startsWith($name, '_')) {
            return true;
        }
    }
    return false;
}

function startsWith($subject, $match) : bool {
    return stripos($subject, $match) === 0;
}
