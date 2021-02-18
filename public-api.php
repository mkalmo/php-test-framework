<?php

require_once 'runner.php';
require_once 'util.php';
require_once 'dsl.php';
require_once 'internals.php';
require_once 'constants.php';
require_once 'browser/Browser.php';
require_once 'browser/page/Form.php';
include_once 'PointsReporter.php';
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

function assertThat($actual, $expected, $message = ''): void {
    if ($actual === $expected) {
        return;
    }

    $message = $message ?? sprintf("Expected %s but was %s",
            stf\asString($expected), stf\asString($actual));

    throw new stf\FrameworkException(ERROR_C02, $message);
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

function logPostParameters(bool $flag) : void {
    getSettings()->logPostParameters = $flag;
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

function assertPageContainsInputWithName($name) : void {
    $field = stf\getForm()->getFieldByName($name);

    if ($field === null) {
        fail(ERROR_W05,
            sprintf("Current page does not contain input with name '%s'.",
                $name));
    }
}

function assertPageContainsButtonWithName($name) : void {
    $field = stf\getForm()->getButtonByName($name);

    if ($field === null) {
        fail(ERROR_W06,
            sprintf("Current page does not contain button with name '%s'.",
                $name));
    }
}

function assertPageContainsLinkWithText($text) : void {
    $link = getBrowser()->getPage()->getLinkByText($text);

    if ($link === null) {
        fail(ERROR_W03,
            sprintf("Current page does not contain link with text '%s'.", $text));
    }
}

function assertPageContainsElementWithId($id) : void {
    $elements = getBrowser()->getPage()->getElements();

    foreach ($elements as $element) {
        if ($element->getId() === $id) {
            return;
        }
    }

    fail(ERROR_W08,
        sprintf("Current page does not contain element with id '%s'.", $id));
}

function assertPageContainsText($textToBeFound) : void {
    $pageText = getBrowser()->getPage()->getText();

    if (strpos($pageText, $textToBeFound) !== false) {
        return;
    }

    fail(ERROR_C01, sprintf("Did not find text %s on current page", $textToBeFound));
}

function assertCurrentUrl($expected) : void {
    $actual = getBrowser()->getCurrentUrl();

    if ($actual !== $expected) {
        fail(ERROR_W10, sprintf("Expected url to be %s but was %s",
            $expected, $actual));
    }
}

function clickLinkByText($text) : void {
    assertPageContainsLinkWithText($text);

    $link = getBrowser()->getPage()->getLinkByText($text);

    getBrowser()->navigateTo($link->getHref());
}

function getLinkHrefByText($text) : string {
    assertPageContainsLinkWithText($text);

    return getBrowser()->getPage()->getLinkByText($text)->getHref();
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
    $field = stf\getForm()->getFieldByName($fieldName);

    if ($field instanceof stf\RadioGroup) {

        // check has option

        // $field->addOption('aaa');
    }

//    stf\getForm()->setFieldValue($fieldName, $value);
}

function assertFieldValue(string $fieldName, string $expected) {
    $actual = stf\getForm()->getFieldByName($fieldName)->getValue();

    if ($actual !== $expected) {
        fail(ERROR_W09, sprintf("Expected value to be '%s' but it was '%s'",
            $expected, $actual));
    }
}
