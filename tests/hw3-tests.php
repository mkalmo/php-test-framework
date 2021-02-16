<?php

require_once '../public-api.php';
require_once '../dsl.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);
logRequests(false);

function submittingFormAddsPersonToList() {
    navigateTo('/');

    clickLinkById('book-form-link');

    $book = getSampleBook();

    setFieldValue('title', $book->title);
    setFieldValue('grade', $book->grade);
    setFieldValue('isRead', $book->isRead);

    clickButton('submitButton');

    assertPageContainsText($book->title);
}

function submittingAuthorFormAddsAuthorToList() {

    navigateTo('/');

    clickLinkById('author-form-link');

    $author = getSampleAuthor();

    setFieldValue('firstName', $author->firstName);
    setFieldValue('lastName', $author->lastName);
    setFieldValue('grade', $author->grade);

    clickButton('submitButton');

    assertPageContainsText($author->firstName);
    assertPageContainsText($author->lastName);
}

function canHandleDifferentSymbolsInBookTitles() {

    navigateTo('/');

    clickLinkById('book-form-link');

    $title = "!.,:;\n" . getSampleBook()->title;

    setFieldValue('title', $title);

    clickButton('submitButton');

    assertPageContainsText($title);
}

function canHandleDifferentSymbolsInAuthorNames() {

    navigateTo('/');

    clickLinkById('author-form-link');

    $firstName = "!.,:;\n" . getSampleAuthor()->firstName;
    $lastName = "!.,:;\n" . getSampleAuthor()->lastName;

    setFieldValue('firstName', $firstName);
    setFieldValue('lastName', $lastName);

    clickButton('submitButton');

    assertPageContainsText($firstName);
    assertPageContainsText($lastName);
}

stf\runTests();
