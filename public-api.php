<?php

list ($phpMajorVersion, $phpMinorVersion) = explode('.', PHP_VERSION);

if (intval($phpMajorVersion) < 7
    || intval($phpMajorVersion) === 7 && intval($phpMinorVersion) < 4) {

    die('This framework requires Php version 7.4 or greater. '.
        "Found Php version " . PHP_VERSION . '.' . PHP_EOL);
}

require_once 'runner.php';
require_once 'util.php';
require_once 'domain.php';
require_once 'internals.php';
require_once 'constants.php';

include_once __DIR__ . '/simpletest/user_agent.php';

require_once 'autoload.php';

use stf\matcher\ContainsMatcher;
use stf\matcher\AbstractMatcher;
use stf\matcher\ContainsStringMatcher;
use stf\matcher\ContainsNotStringMatcher;

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

function assertThat($actual, stf\matcher\AbstractMatcher $matcher, $message = null): void {
    if ($matcher->matches($actual)) {
        return;
    }

    if ($message) {
        throw new stf\FrameworkException(ERROR_C01, $message);
    }

    $error = $matcher->getError($actual);

    throw new stf\FrameworkException($error->getCode(), $error->getMessage());
}

function setBaseUrl(string $url) : void {
    stf\getGlobals()->baseUrl = new stf\browser\Url($url);
    stf\getGlobals()->currentUrl = new stf\browser\Url($url);
}

function setLogRequests(bool $flag) : void {
    stf\getGlobals()->logRequests = $flag;
}

function setLogPostParameters(bool $flag) : void {
    stf\getGlobals()->logPostParameters = $flag;
}

function setPrintStackTrace(bool $flag) : void {
    stf\getGlobals()->printStackTrace = $flag;
}

function setPrintPageSourceOnParseError(bool $flag) : void {
    stf\getGlobals()->printPageSourceOnParseError = $flag;
}

function getResponseCode() : int {
    return stf\getGlobals()->responseCode;
}

function getCurrentUrl() : string {
    return stf\getGlobals()->currentUrl->asString();
}

function printPageSource() : void {
    print stf\getGlobals()->page->getSource() . PHP_EOL;
}

function printPageText() : void {
    print getPageText() . PHP_EOL;
}

function getPageText() : string {
    return stf\getGlobals()->page->getText();
}

function assertPageContainsLinkWithId($linkId) : void {
    $link = stf\getGlobals()->page->getLinkById($linkId);

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

function assertPageContainsSelectWithName($name) : void {
    if (stf\getForm()->getSelectByName($name) !== null) {
        return;
    }

    fail(ERROR_W16,
        sprintf("Current page does not contain select with name '%s'.", $name));
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

function assertPageDoesNotContainButtonWithName($name) : void {
    if (stf\getForm()->getButtonByName($name) === null) {
        return;
    }

    fail(ERROR_W19,
        sprintf("Current page should not contain button with name '%s'.", $name));
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
    $link = stf\getGlobals()->page->getLinkByText($text);

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

    fail(ERROR_W09,
        sprintf("Current page should not contain element with id '%s'.", $id));
}

function assertPageContainsText($textToBeFound) : void {
    $pageText = stf\getGlobals()->page->getText();

    if (strpos($pageText, $textToBeFound) !== false) {
        return;
    }

    fail(ERROR_H04, sprintf("Did not find text '%s' on the current page.",
        $textToBeFound));
}

function assertCurrentUrl($expected) : void {
    $actual = stf\getGlobals()->currentUrl->asString();

    if ($actual !== $expected) {
        fail(ERROR_H03, sprintf("Expected url to be '%s' but was '%s'",
            $expected, $actual));
    }
}

function clickLinkWithText($text) : void {
    assertPageContainsLinkWithText($text);

    $link = stf\getGlobals()->page->getLinkByText($text);

    stf\navigateTo($link->getHref());
}

function getHrefFromLinkWithText($text) : string {
    assertPageContainsLinkWithText($text);

    return stf\getGlobals()->page->getLinkByText($text)->getHref();
}

function clickLinkWithId($linkId) : void {
    assertPageContainsLinkWithId($linkId);

    $link = stf\getGlobals()->page->getLinkById($linkId);

    navigateTo($link->getHref());
}

function navigateTo(string $url) {
    stf\navigateTo($url);
}

function clickButton(string $buttonName, ?string $buttonValue = null) {
    stf\submitFormByButtonPress($buttonName, $buttonValue);
}

function setTextFieldValue(string $fieldName, string $value) {
    assertPageContainsTextFieldWithName($fieldName);

    stf\getForm()->getTextFieldByName($fieldName)->setValue($value);
}

function forceFieldValue(string $fieldName, string $value) {
    stf\getForm()->deleteFieldByName($fieldName);

    stf\getForm()->addTextField($fieldName, $value);
}

function selectOptionWithText(string $fieldName, string $text) {
    assertPageContainsSelectWithName($fieldName);

    stf\getForm()->getSelectByName($fieldName)->selectOptionWithText($text);
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

    return $field instanceof stf\browser\page\Checkbox
        ? $field->isChecked()
        : $field->getValue();
}

function getSelectedOptionText(string $fieldName) : string {
    assertPageContainsSelectWithName($fieldName);

    $select = stf\getForm()->getSelectByName($fieldName);

    return $select->getSelectedOptionText();
}

function is($value) : stf\matcher\AbstractMatcher {
    return new stf\matcher\IsMatcher($value);
}

function contains(array $needleArray) : AbstractMatcher {
    return new ContainsMatcher($needleArray);
}

function containsString(string $needle) : AbstractMatcher {
    return new ContainsStringMatcher($needle);
}

function doesNotContainString(string $needle) : AbstractMatcher {
    return new ContainsNotStringMatcher($needle);
}

function containsStringOnce($value) : stf\matcher\AbstractMatcher {
    return new stf\matcher\ContainsStringOnceMatcher($value);
}
