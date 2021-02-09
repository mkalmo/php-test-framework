<?php

require_once 'runner.php';
require_once 'util.php';
require_once 'dsl.php';
require_once 'constants.php';
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

function fail($code, $message): void {
    throw new stf\FrameworkException($code, $message);
}

function assertThat($actual, $expected): void {
    if ($actual !== $expected) {
        throw new stf\FrameworkException(ERROR_C02,
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

function getResponseCode() : int {
    return getBrowser()->getResponseCode();
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

function assertPageContainsLinkWithId($linkId) : void {
    $link = getBrowser()->getPage()->getLinkById($linkId);

    if ($link === null) {
        fail(ERROR_W03,
            sprintf("Current page does not contain link with id '%s'.", $linkId));
    }


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
    assertPageContainsLinkWithId($linkId);

    $link = getBrowser()->getPage()->getLinkById($linkId);

    navigateTo($link->getHref());
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
