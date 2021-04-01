<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);

function repositoryContainsIndex() {
    navigateTo('/');

    if (getResponseCode() !== 200) {
        fail(ERROR_C01, "Did not find file named index.html from root directory");
    }
}

function defaultPageIsBookList() {
    navigateTo('/');

    assertThat(getPageId(), is('book-list-page'));
}

function bookListPageContainsCorrectMenu() {
    navigateTo('/');

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');
}

function bookFormPageContainsCorrectElements() {
    navigateTo('/');

    clickLinkWithId('book-form-link');

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');

    assertPageContainsTextFieldWithName('title');
    assertPageContainsRadioWithName('grade');
    assertPageContainsCheckboxWithName('isRead');
    assertPageContainsButtonWithName('submitButton');
}

function authorListPageContainsCorrectMenu() {
    navigateTo('/');

    clickLinkWithId('author-list-link');

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');
}

function authorFormPageContainsCorrectElements() {
    navigateTo('/');

    clickLinkWithId('author-form-link');

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');

    assertPageContainsTextFieldWithName('firstName');
    assertPageContainsTextFieldWithName('lastName');
    assertPageContainsRadioWithName('grade');
    assertPageContainsButtonWithName('submitButton');
}

function repositoryContainsCssFile() {
    global $argc, $argv;

    if ($argc < 2) {
        die('Pass directory to scan as an argument' . PHP_EOL);
    } else {
        $path = realpath($argv[1]);
    }

    if ($path === false) {
        die('Argument is not a correct directory' . PHP_EOL);
    }

    $it = new RecursiveDirectoryIterator($path);
    $it = new RecursiveIteratorIterator($it);
    $it = new RegexIterator($it, '/\.(\w+)$/i', RecursiveRegexIterator::GET_MATCH);

    foreach($it as $each) {
        $extension = strtolower($each[1]);
        if ($extension === 'css') {
            return;
        };
    }

    fail(ERROR_C01, "Did not find css file from repository");
}

stf\runTests(new stf\PointsReporter([7 => 5]));
