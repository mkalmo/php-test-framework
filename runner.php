<?php

namespace stf;

require_once 'browser/parser/ParseException.php';

use \Exception;
use \RuntimeException;
use tplLib\ParseException;

function runTests(?PointsReporter $reporter = null) {
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
            printf("Stack trace: \n%s\n", getStackTrace($ex, $testName));
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

function getStackTrace(Exception $ex, string $testName) : string {
    $result = '';

    // print $ex;

    foreach ($ex->getTrace() as $line) {
        $functionName = $line['function'] ?? '';
        $lineNr = $line['line'] ?? '';
        $filePath = $line['file'] ?? '';

        if ($functionName === $testName) {
            break;
        }

        $result .= sprintf("  %s() on line %s: %s(%s)\n",
            $functionName, $lineNr,  $filePath, $lineNr);
    }

    return $result;
}

function getTestNames() : array {
    $testFilePath = get_included_files()[0];

    $testFileSource = file_get_contents($testFilePath);

    $testNames = getFunctionNames($testFileSource);

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

function getFunctionNames(string $src): array {

    $tokens = token_get_all($src);

    $result = [];
    while (count($tokens)) {
        $token = array_shift($tokens);

        if (is_array($token)
            && token_name($token[0]) === 'T_COMMENT'
            && strpos($token[1], '#Helpers') !== false) {

            return $result;
        }

        if (is_array($token) && token_name($token[0]) === 'T_FUNCTION') {
            $token = array_shift($tokens);
            if (is_array($token) && token_name($token[0]) === 'T_WHITESPACE') {
                $token = array_shift($tokens);
            }
            if ($token === '(') { // anonymous function
                continue;
            } else if (is_array($token) && token_name($token[0]) === 'T_STRING') {
                $result[] = $token[1];
            } else {
                throw new RuntimeException('Unexpected error');
            }
        }
    }

    return $result;
}
