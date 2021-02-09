<?php

require_once 'runner.php';
require_once 'util.php';
require_once 'browser/Browser.php';
require_once 'Settings.php';

function assertThrows($function): void {
    try {
        $function();
    } catch (Throwable $t) {
        return;
    }

    throw new AssertionError("Expected to throw but did not");
}

function fail($message): void {
    throw new AssertionError($message);
}

function assertThat($actual, $expected): void {
    if ($actual !== $expected) {
        throw new AssertionError(
            sprintf("Expected %s but was %s",
                stf\asString($expected), stf\asString($actual)));
    }
}

function is($value) {
    return $value;
}

function setBaseUrl(string $url) : void {
    getBrowser()->setCurrentUrl($url);
}

function logRequests(bool $flag) : void {
    getSettings()->logRequests = $flag;
}

function getBrowser() : stf\Browser {
    $key = "---STF-BROWSER---";

    return $GLOBALS[$key] = $GLOBALS[$key] ?? new stf\Browser(getSettings());
}

function getSettings() : stf\Settings {
    $key = "---STF-SETTINGS---";

    return $GLOBALS[$key] = $GLOBALS[$key] ?? new stf\Settings();
}

function getCurrentUrl() : string {
    return getBrowser()->getCurrentUrl();
}

function printPageSource() : void {
    $page = getBrowser()->getPage();
    print $page->getSource() . PHP_EOL;
}

function printPageText() : void {
    $page = getBrowser()->getPage();
    print $page->getText() . PHP_EOL;
}

function assertPageContainsText($textToBeFound) : void {
    $pageText = getBrowser()->getPage()->getText();

    if (strpos($pageText, $textToBeFound) !== false) {
        return;
    }

    fail(sprintf("Did not find text %s on current page", $textToBeFound));
}

function assertCurrentUrl($expected) : void {
    $actual = getBrowser()->getCurrentUrl();

    assertThat($actual, is($expected));
}

function clickLinkByText($text) : void {

    $link = getBrowser()->getPage()->getLinkByText($text);

    if ($link === null) {
        throw new RuntimeException('no link with text: ' . $text);
    }

    getBrowser()->navigateTo($link->getHref());
}

function clickLinkById($linkId) : void {

    $link = getBrowser()->getPage()->getLinkById($linkId);


    if ($link === null) {
        try {
            error('no link with id: ' . $linkId);
        } catch (RuntimeException $e) {
            var_dump($e->getTrace());

            print $e->getMessage() . PHP_EOL;
        }

    }

//    getBrowser()->navigateTo($link->getHref());
}

function error($message) {
    throw new RuntimeException($message);
}

function navigateTo(string $url) {
    getBrowser()->navigateTo($url);
}

function clickButton(string $buttonName) {
    getBrowser()->submitFormByButtonPress($buttonName);
}

function setFieldValue(string $fieldName, string $value) {
    $page = getBrowser()->getPage();

    if (!$page->containsForm()) {
        throw new RuntimeException("Page does not contain a form");
    }

    $form = $page->getForm();

    $form->setFieldValue($fieldName, $value);
}
