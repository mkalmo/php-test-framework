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

require_once 'matchers/ContainsMatcher.php';
require_once 'matchers/ContainsNotMatcher.php';
require_once 'matchers/IsMatcher.php';

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

function assertThat($actual, stf\AbstractMatcher $matcher, $error = ''): void {
    if ($matcher->matches($actual)) {
        return;
    }

    $error = $matcher->getError($actual);

    throw new stf\FrameworkException($error->getCode(), $error->getMessage());
}

function is($value) : stf\AbstractMatcher {
    return new stf\IsMatcher($value);
}

function setBaseUrl(string $url) : void {
    stf\getBrowser()->setCurrentUrl($url);
}

function logRequests(bool $flag) : void {
    stf\getSettings()->logRequests = $flag;
}

function logPostParameters(bool $flag) : void {
    stf\getSettings()->logPostParameters = $flag;
}

function getResponseCode() : int {
    return stf\getBrowser()->getResponseCode();
}

function getCurrentUrl() : string {
    return stf\getBrowser()->getCurrentUrl();
}

function printPageSource() : void {
    $page = stf\getBrowser()->getPage();
    print $page->getSource() . PHP_EOL;
}

function printPageText() : void {
    print getPageText() . PHP_EOL;
}

function getPageText() : string {
    return stf\getBrowser()->getPage()->getText();
}

function assertPageContainsLinkWithId($linkId) : void {
    $link = stf\getBrowser()->getPage()->getLinkById($linkId);

    if ($link === null) {
        fail(ERROR_W03,
            sprintf("Current page does not contain link with id '%s'.", $linkId));
    }
}

function assertPageContainsTextFieldWithName($name) : void {
    if (stf\getForm()->getTextFieldByName($name) !== null) {
        return;
    }

    fail(ERROR_W13,
        sprintf("Current page does not contain text field with name '%s'.", $name));
}

function assertPageContainsRadioWithName($name) : void {
    if (stf\getForm()->getRadioByName($name) !== null) {
        return;
    }

    fail(ERROR_W14,
        sprintf("Current page does not contain radio with name '%s'.", $name));
}

function assertPageContainsFieldWithName($name) : void {
    if (stf\getForm()->getFieldByName($name) !== null) {
        return;
    }

    fail(ERROR_W05,
        sprintf("Current page does not contain field with name '%s'.", $name));
}

function assertPageDoesNotContainFieldWithName($name) : void {
    if (stf\getForm()->getFieldByName($name) === null) {
        return;
    }

    fail(ERROR_W18,
        sprintf("Current page should not contain field with name '%s'.", $name));
}

function assertPageContainsCheckboxWithName($name) : void {
    if (stf\getForm()->getCheckboxByName($name) !== null) {
        return;
    }

    fail(ERROR_W15,
        sprintf("Current page does not contain checkbox with name '%s'.", $name));
}

function assertPageContainsButtonWithName($name) : void {
    if (stf\getForm()->getButtonByName($name) !== null) {
        return;
    }

    fail(ERROR_W06,
        sprintf("Current page does not contain button with name '%s'.",
            $name));
}

function assertPageContainsLinkWithText($text) : void {
    $link = stf\getBrowser()->getPage()->getLinkByText($text);

    if ($link === null) {
        fail(ERROR_W03,
            sprintf("Current page does not contain link with text '%s'.", $text));
    }
}

function assertPageContainsElementWithId($id) : void {
    $element = stf\getElementWithId($id);

    if ($element) {
        return;
    }

    fail(ERROR_W08,
        sprintf("Current page does not contain element with id '%s'.", $id));
}

function assertPageDoesNotContainElementWithId($id) : void {
    $element = stf\getElementWithId($id);

    if (!$element) {
        return;
    }

    fail(ERROR_W17,
        sprintf("Current page should not contain element with id '%s'.", $id));
}

function assertPageContainsText($textToBeFound) : void {
    $pageText = stf\getBrowser()->getPage()->getText();

    if (strpos($pageText, $textToBeFound) !== false) {
        return;
    }

    fail(ERROR_C01, sprintf("Did not find text %s on current page", $textToBeFound));
}

function assertCurrentUrl($expected) : void {
    $actual = stf\getBrowser()->getCurrentUrl();

    if ($actual !== $expected) {
        fail(ERROR_W10, sprintf("Expected url to be %s but was %s",
            $expected, $actual));
    }
}

function clickLinkWithText($text) : void {
    assertPageContainsLinkWithText($text);

    $link = stf\getBrowser()->getPage()->getLinkByText($text);

    stf\getBrowser()->navigateTo($link->getHref());
}

function getHrefFromLinkWithText($text) : string {
    assertPageContainsLinkWithText($text);

    return stf\getBrowser()->getPage()->getLinkByText($text)->getHref();
}

function clickLinkWithId($linkId) : void {
    assertPageContainsLinkWithId($linkId);

    $link = stf\getBrowser()->getPage()->getLinkById($linkId);

    navigateTo($link->getHref());
}

function navigateTo(string $url) {
    stf\getBrowser()->navigateTo($url);
}

function clickButton(string $buttonName) {
    stf\getBrowser()->submitFormByButtonPress($buttonName);
}

function setTextFieldValue(string $fieldName, string $value) {
    assertPageContainsTextFieldWithName($fieldName);

    stf\getForm()->getTextFieldByName($fieldName)->setValue($value);
}

function setCheckboxValue(string $fieldName, bool $value) {
    assertPageContainsCheckboxWithName($fieldName);

    stf\getForm()->getCheckboxByName($fieldName)->check($value);
}

function setRadioFieldValue(string $fieldName, string $value) {
    assertPageContainsRadioWithName($fieldName);

    $field = stf\getForm()->getRadioByName($fieldName);

    if ($field->hasOption($value)) {
        $field->selectOption($value);
    } else {
        fail(ERROR_W11, sprintf("radio with name '%s' does not have option '%s'",
            $fieldName, $value));
    }

}

function getFieldValue(string $fieldName) {
    assertPageContainsFieldWithName($fieldName);

    $field = stf\getForm()->getFieldByName($fieldName);

    return $field instanceof stf\Checkbox
        ? $field->isChecked()
        : $field->getValue();
}

function assertFieldValue(string $fieldName, string $expected) {
    assertPageContainsFieldWithName($fieldName);

    $field = stf\getForm()->getFieldByName($fieldName);

    $actual = $field instanceof stf\Checkbox ? $field->isChecked() : $field->getValue();

    if ($actual !== $expected) {
        fail(ERROR_W09, sprintf("Expected value to be '%s' but it was '%s'",
            $expected, $actual));
    }
}

function containsString(string $needle) : stf\AbstractMatcher {
    return new stf\ContainsMatcher($needle);
}

function doesNotContainString(string $needle) : stf\AbstractMatcher {
    return new stf\ContainsNotMatcher($needle);
}