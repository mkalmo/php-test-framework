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

use stf\browser\page\FieldType;
use stf\Globals;
use stf\matcher\ContainsMatcher;
use stf\matcher\AbstractMatcher;
use stf\matcher\ContainsStringMatcher;
use stf\matcher\ContainsNotStringMatcher;
use stf\browser\Browser;

function getBrowser() : Browser {
    return getGlobals()->browser;
}

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
    getBrowser()->maxRedirectCount = 0;
}

function setBaseUrl(string $url) : void {
    getGlobals()->baseUrl = new stf\browser\Url($url);
    getBrowser()->setCurrentUrl($url);
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
    return getBrowser()->getCurrentUrl();
}

function printPageSource() : void {
    print getPageSource() . PHP_EOL;
}

function printPageText() : void {
    print getPageText() . PHP_EOL;
}

function getPageText() : string {
    return getBrowser()->getPageText();
}

function getPageSource() : string {
    return getBrowser()->getPageSource();
}

function assertPageContainsLinkWithId($linkId) : void {
    if (getBrowser()->hasLinkWithId($linkId)) {
        return;
    }

    fail(ERROR_W03,
        sprintf("Current page does not contain link with id '%s'.", $linkId));
}

function assertPageContainsTextFieldWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::TextField)) {
        return;
    }

    fail(ERROR_W13,
        sprintf("Current page does not contain text field with name '%s'.", $name));
}

function assertPageContainsRadioWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::Radio)) {
        return;
    }

    fail(ERROR_W14,
        sprintf("Current page does not contain radio with name '%s'.", $name));
}

function assertPageContainsSelectWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::Select)) {
        return;
    }

    fail(ERROR_W16,
        sprintf("Current page does not contain select with name '%s'.", $name));
}

function assertPageContainsFieldWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::Any)) {
        return;
    }

    fail(ERROR_W05,
        sprintf("Current page does not contain field with name '%s'.", $name));
}

function assertPageDoesNotContainFieldWithName($name) : void {
    if (!getBrowser()->hasFieldByName($name, FieldType::Any)) {
        return;
    }

    fail(ERROR_W18,
        sprintf("Current page should not contain field with name '%s'.", $name));
}

function assertPageDoesNotContainButtonWithName($name) : void {
    if (!getBrowser()->hasFieldByName($name, FieldType::Button)) {
        return;
    }

    fail(ERROR_W19,
        sprintf("Current page should not contain button with name '%s'.", $name));
}

function assertPageContainsCheckboxWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::Checkbox)) {
        return;
    }

    fail(ERROR_W15,
        sprintf("Current page does not contain checkbox with name '%s'.", $name));
}

function assertPageContainsButtonWithName($name) : void {
    if (getBrowser()->hasFieldByName($name, FieldType::Button)) {
        return;
    }

    fail(ERROR_W06,
        sprintf("Current page does not contain button with name '%s'.",
            $name));
}

function assertPageContainsLinkWithText($text) : void {
    if (getBrowser()->hasLinkWithText($text)) {
        return;
    }

    fail(ERROR_W04,
        sprintf("Current page does not contain link with text '%s'.", $text));
}

function assertPageContainsElementWithId($id) : void {
    if (getBrowser()->hasElementWithId($id)) {
        return;
    }

    fail(ERROR_W08,
        sprintf("Current page does not contain element with id '%s'.", $id));
}

function assertPageDoesNotContainElementWithId($id) : void {
    if (!getBrowser()->hasElementWithId($id)) {
        return;
    }

    fail(ERROR_W09,
        sprintf("Current page should not contain element with id '%s'.", $id));
}

function assertFrontControllerLink(string $id) : void {
    assertPageContainsLinkWithId($id);

    $link = getBrowser()->getLinkHrefById($id);

    $pattern = '/^(index\.php)?\??[-=&\w]*$/';

    if (!preg_match($pattern, $link)) {
        $message = 'Front Controller pattern expects all links '
            . 'to be in ?key1=value1&key2=... format. But this link was: ' . $link;

        fail(ERROR_W20, $message);
    }
}

function assertPageContainsText($textToBeFound) : void {
    if (strpos(getPageText(), $textToBeFound) !== false) {
        return;
    }

    fail(ERROR_H04, sprintf("Did not find text '%s' on the current page.",
        $textToBeFound));
}

function assertNoOutput() : void {
    $source = getPageSource();

    if (preg_match('/^\s*$/', $source)) {
        return;
    }

    fail(ERROR_W21, sprintf(
        "Should not print any output along with redirect header " .
        "but the output was: %s", $source));
}

function assertCurrentUrl($expected) : void {
    $actual = getBrowser()->getCurrentUrl();

    if ($actual !== $expected) {
        fail(ERROR_H03, sprintf("Expected url to be '%s' but was '%s'",
            $expected, $actual));
    }
}

function clickLinkWithText($text) : void {
    assertPageContainsLinkWithText($text);

    getBrowser()->clickLinkWithText($text);
}

function getHrefFromLinkWithText(string $text) : string {
    assertPageContainsLinkWithText($text);

    return getBrowser()->getLinkHrefByText($text);
}

function clickLinkWithId($linkId) : void {
    assertPageContainsLinkWithId($linkId);

    getBrowser()->clickLinkWithId($linkId);
}

function navigateTo(string $url) {
    getBrowser()->navigateTo($url);
}

function clickButton(string $buttonName, ?string $buttonValue = null) {
    assertPageContainsButtonWithName($buttonName);

    getBrowser()->submitFormByButtonPress($buttonName, $buttonValue);
}

function setTextFieldValue(string $fieldName, string $value) {
    assertPageContainsTextFieldWithName($fieldName);

    getBrowser()->setTextFieldValue($fieldName, $value);
}

function forceFieldValue(string $fieldName, string $value) {
    assertPageContainsFieldWithName($fieldName);

    getBrowser()->forceFieldValue($fieldName, $value);
}

function selectOptionWithText(string $fieldName, string $text) : void {
    assertPageContainsSelectWithName($fieldName);

    if (getBrowser()->hasSelectOptionWithLabel($fieldName, $text)) {
        getBrowser()->selectOptionWithLabel($fieldName, $text);
    } else {
        fail(ERROR_W12, sprintf("select with name '%s' does not have option '%s'",
            $fieldName, $text));
    }
}

function setCheckboxValue(string $fieldName, bool $value) {
    assertPageContainsCheckboxWithName($fieldName);

    getBrowser()->setCheckboxValue($fieldName, $value);
}

function setRadioFieldValue(string $fieldName, string $value) {
    assertPageContainsRadioWithName($fieldName);

    if (getBrowser()->hasRadioOption($fieldName, $value)) {
        getBrowser()->setRadioValue($fieldName, $value);
    } else {
        fail(ERROR_W11, sprintf("radio with name '%s' does not have option '%s'",
            $fieldName, $value));
    }
}

function getFieldValue(string $fieldName) {
    assertPageContainsFieldWithName($fieldName);

    return getBrowser()->getFieldValue($fieldName);
}

function getSelectedOptionText(string $fieldName) : string {
    assertPageContainsSelectWithName($fieldName);

    return getBrowser()->getSelectedOptionText($fieldName);
}

function deleteSessionCookie() : void {
    throw new Error('not implemented');
    // getGlobals()->httpClient->deleteCookie(session_name());
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
    $path = getProjectPath($argv, $userDefinedDir);

    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}

function getProjectPath(array $argv, string $userDefinedDir) {
    $path = count($argv) === 2 ? $argv[1] : $userDefinedDir;

    if (!$path) {
        die("Please specify your project's directory in constant PROJECT_DIRECTORY");
    }

    $path = realpath($path);

    if (!file_exists($path)) {
        die("Value in PROJECT_DIRECTORY is not correct directory");
    }

    return $path;
}

function getGlobals() : Globals {
    $key = "---STF-GLOBALS---";

    if (!isset($GLOBALS[$key])) {
        $GLOBALS[$key] = new Globals();
    }

    return $GLOBALS[$key];
}
