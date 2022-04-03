<?php

require_once 'vendor/php-test-framework/public-api.php';
require_once 'vendor/php-test-framework/webdriver-common.php';

const BASE_URL = 'http://localhost:8080/hw8/';

function baseUrlResponds() {
    getInChrome(BASE_URL);

    closeBrowser();
}

function startPageHasMenuWithCorrectLinks() {
    getInChrome(BASE_URL);

    assertLinkById('book-list-link');
    assertLinkById('book-form-link');
    assertLinkById('author-list-link');
    assertLinkById('author-form-link');

    closeBrowser();
}

function canSaveBooks() {

    getInChrome(BASE_URL);

    clickLinkById('book-form-link');

    $book = getSampleBook();

    setFieldByName('title', $book->title);

    clickByName('submitButton');

    assertSingleMatch($book->title);

    closeBrowser();

    // check that data is not generated on server side

    navigateTo(BASE_URL);
    assertThat(getPageText(), doesNotContainString($book->title));
}

function canUpdateBooks() {

    getInChrome(BASE_URL);

    clickLinkById('book-form-link');

    $title = getSampleBook()->title;
    $newTitle = getSampleBook()->title;

    setFieldByName('title', $title);

    clickByName('submitButton');

    clickLinkByText($title);

    setFieldByName('title', $newTitle);

    clickByName('submitButton');

    assertSingleMatch($newTitle);
    assertNoText($title);

    closeBrowser();
}

function canDeleteInsertedBooks() {

    getInChrome(BASE_URL);

    clickLinkById('book-form-link');

    $book = getSampleBook();

    setFieldByName('title', $book->title);

    clickByName('submitButton');

    clickLinkByText($book->title);

    clickByName('deleteButton');

    assertNoText($book->title);

    closeBrowser();
}

setBaseUrl(BASE_URL);

stf\runTests(new stf\PointsReporter([5 => 5]));