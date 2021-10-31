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
use function stf\getGlobals;
use function stf\getFormSet;

function assertThrows($function): void {
    try {
        $function();
    } catch (Throwable $t) {
        return;
    }

    throw new stf\FrameworkException(ERROR_C01, "Expected to throw but did not");
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

function disableAutomaticRedirects() : void {
    getGlobals()->maxRedirectCount = 0;
}

function setBaseUrl(string $url) : void {
    getGlobals()->baseUrl = new stf\browser\Url($url);
    getGlobals()->currentUrl = new stf\browser\Url($url);
}

function setLogRequests(bool $flag) : void {
    getGlobals()->logRequests = $flag;
}

function setLogPostParameters(bool $flag) : void {
    getGlobals()->logPostParameters = $flag;
}

function setPrintStackTrace(bool $flag) : void {
    getGlobals()->printStackTrace = $flag;
}

function setPrintPageSourceOnError(bool $flag) : void {
    getGlobals()->printPageSourceOnError = $flag;
}

function getResponseCode() : int {
    return getGlobals()->responseCode;
}

function getCurrentUrl() : string {
    return getGlobals()->currentUrl->asString();
}

function printPageSource() : void {
    print getGlobals()->page->getSource() . PHP_EOL;
}

function printPageText() : void {
    print getPageText() . PHP_EOL;
}

function getPageText() : string {
    return getGlobals()->page->getText();
}

function getPageSource() : string {
    return getGlobals()->page->getSource();
}

function assertPageContainsLinkWithId($linkId) : void {
    $link = getGlobals()->page->getLinkById($linkId);

    if ($link === null) {
        fail(ERROR_W03,
            sprintf("Current page does not contain link with id '%s'.", $linkId));
    }
}

function assertPageContainsTextFieldWithName($name) : void {
    if (getFormSet()->getTextFieldByName($name) !== null) {
        return;
    }

    fail(ERROR_W13,
        sprintf("Current page does not contain text field with name '%s'.", $name));
}

function assertPageContainsRadioWithName($name) : void {
    if (getFormSet()->getRadioByName($name) !== null) {
        return;
    }

    fail(ERROR_W14,
        sprintf("Current page does not contain radio with name '%s'.", $name));
}

function assertPageContainsSelectWithName($name) : void {
    if (getFormSet()->getSelectByName($name) !== null) {
        return;
    }

    fail(ERROR_W16,
        sprintf("Current page does not contain select with name '%s'.", $name));
}

function assertPageContainsFieldWithName($name) : void {
    if (getFormSet()->getFieldByName($name) !== null) {
        return;
    }

    fail(ERROR_W05,
        sprintf("Current page does not contain field with name '%s'.", $name));
}

function assertPageDoesNotContainFieldWithName($name) : void {
    if (getFormSet()->getFieldByName($name) === null) {
        return;
    }

    fail(ERROR_W18,
        sprintf("Current page should not contain field with name '%s'.", $name));
}

function assertPageDoesNotContainButtonWithName($name) : void {
    if (getFormSet()->getButtonByName($name) === null) {
        return;
    }

    fail(ERROR_W19,
        sprintf("Current page should not contain button with name '%s'.", $name));
}

function assertPageContainsCheckboxWithName($name) : void {
    if (getFormSet()->getCheckboxByName($name) !== null) {
        return;
    }

    fail(ERROR_W15,
        sprintf("Current page does not contain checkbox with name '%s'.", $name));
}

function assertPageContainsButtonWithName($name) : void {
    if (getFormSet()->getButtonByName($name) !== null) {
        return;
    }

    fail(ERROR_W06,
        sprintf("Current page does not contain button with name '%s'.",
            $name));
}

function assertPageContainsLinkWithText($text) : void {
    $link = getGlobals()->page->getLinkByText($text);

    if ($link === null) {
        fail(ERROR_W04,
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

function assertFrontControllerLink(string $id) : void {
    assertPageContainsLinkWithId($id);

    $link = getGlobals()->page->getLinkById($id)->getHref();

    $pattern = '/^(index\.php)?\??[-=&\w]*$/';

    if (!preg_match($pattern, $link)) {
        $message = 'Front Controller pattern expects all links '
            . 'to be in ?key1=value1&key2=... format. But this link was: ' . $link;

        fail(ERROR_W20, $message);
    }
}

function assertPageContainsText($textToBeFound) : void {
    $pageText = getGlobals()->page->getText();

    if (strpos($pageText, $textToBeFound) !== false) {
        return;
    }

    fail(ERROR_H04, sprintf("Did not find text '%s' on the current page.",
        $textToBeFound));
}

function assertNoOutput() : void {
    $source = getGlobals()->page->getSource();

    if (preg_match('/^\s*$/', $source)) {
        return;
    }

    fail(ERROR_W21, sprintf(
        "Should not print any output along with redirect header " .
        "but the output was: %s", $source));
}

function assertCurrentUrl($expected) : void {
    $actual = getGlobals()->currentUrl->asString();

    if ($actual !== $expected) {
        fail(ERROR_H03, sprintf("Expected url to be '%s' but was '%s'",
            $expected, $actual));
    }
}

function clickLinkWithText($text) : void {
    assertPageContainsLinkWithText($text);

    $link = getGlobals()->page->getLinkByText($text);

    stf\navigateTo($link->getHref());
}

function getHrefFromLinkWithText(string $text) : string {
    assertPageContainsLinkWithText($text);

    return getGlobals()->page->getLinkByText($text)->getHref();
}

function getTextFromLinkWithId(string $id) : string {
    assertPageContainsLinkWithId($id);

    return getGlobals()->page->getLinkById($id)->getText();
}

function clickLinkWithId($linkId) : void {
    assertPageContainsLinkWithId($linkId);

    $link = getGlobals()->page->getLinkById($linkId);

    navigateTo($link->getHref());
}

function navigateTo(string $url) {
    stf\navigateTo($url);
}

function clickButton(string $buttonName, ?string $buttonValue = null) {
    assertPageContainsButtonWithName($buttonName);

    stf\submitFormByButtonPress($buttonName, $buttonValue);
}

function setTextFieldValue(string $fieldName, string $value) {
    assertPageContainsTextFieldWithName($fieldName);

    getFormSet()->getTextFieldByName($fieldName)->setValue($value);
}

function forceFieldValue(string $fieldName, string $value) {
    assertPageContainsFieldWithName($fieldName);

    $form = getFormSet()->findFormContainingField($fieldName);

    $form->deleteFieldByName($fieldName);

    $form->addTextField($fieldName, $value);
}

function selectOptionWithText(string $fieldName, string $text) {
    assertPageContainsSelectWithName($fieldName);

    $select = stf\getFormSet()->getSelectByName($fieldName);

    if ($select->hasOptionWithLabel($text)) {
        $select->selectOptionWithText($text);
    } else {
        fail(ERROR_W12, sprintf("select with name '%s' does not have option '%s'",
            $fieldName, $text));
    }

    getFormSet()->getSelectByName($fieldName)->selectOptionWithText($text);
}

function setCheckboxValue(string $fieldName, bool $value) {
    assertPageContainsCheckboxWithName($fieldName);

    getFormSet()->getCheckboxByName($fieldName)->check($value);
}

function setRadioFieldValue(string $fieldName, string $value) {
    assertPageContainsRadioWithName($fieldName);

    $field = getFormSet()->getRadioByName($fieldName);

    if ($field->hasOption($value)) {
        $field->selectOption($value);
    } else {
        fail(ERROR_W11, sprintf("radio with name '%s' does not have option '%s'",
            $fieldName, $value));
    }

}

function getFieldValue(string $fieldName) {
    assertPageContainsFieldWithName($fieldName);

    $field = getFormSet()->getFieldByName($fieldName);

    return $field instanceof stf\browser\page\Checkbox
        ? $field->isChecked()
        : $field->getValue();
}

function getButtonLabel(string $buttonName) : string {
    assertPageContainsButtonWithName($buttonName);

    return getFormSet()->getButtonByName($buttonName)->getLabel();
}

function getSelectedOptionText(string $fieldName) : string {
    assertPageContainsSelectWithName($fieldName);

    $select = getFormSet()->getSelectByName($fieldName);

    return $select->getSelectedOptionText();
}

function deleteSessionCookie() : void {
    getGlobals()->httpClient->deleteCookie(session_name());
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

function containsStringOnce(string $value) : stf\matcher\AbstractMatcher {
    return new stf\matcher\ContainsStringOnceMatcher($value);
}

function containsInAnyOrder(array $value) : stf\matcher\AbstractMatcher {
    return new stf\matcher\ContainsInAnyOrderMatcher($value);
}

function isAnyOf(...$values) : stf\matcher\AbstractMatcher {
    return new stf\matcher\ContainsAnyMatcher($values);
}

function extendIncludePath(array $argv, string $userDefinedDir) {
    $path = count($argv) === 2 ? $argv[1] : $userDefinedDir;

    if (!$path) {
        die("Please specify your project's directory in constant PROJECT_DIRECTORY");
    }

    $path = realpath($path);

    if (!file_exists($path)) {
        die("Value in PROJECT_DIRECTORY is not correct directory");
    }

    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}
