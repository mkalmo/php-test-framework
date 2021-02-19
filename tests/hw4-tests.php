<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);
logRequests(false);

function submittingFormAddsPersonToList() {
    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);
    setRadioFieldValue('grade', $book->grade);
    setCheckboxValue('isRead', $book->isRead);

    clickButton('submitButton');

    assertPageContainsText($book->title);
}

function submittingAuthorFormAddsAuthorToList() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);
    setRadioFieldValue('grade', $author->grade);

    clickButton('submitButton');

    assertPageContainsText($author->firstName);
    assertPageContainsText($author->lastName);
}

function canHandleDifferentSymbolsInBookTitles() {

    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    $title = "!.,:;\n" . getSampleBook()->title;

    setTextFieldValue('title', $title);

    clickButton('submitButton');

    assertPageContainsText($title);
}

function canHandleDifferentSymbolsInAuthorNames() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    $firstName = "!.,:;\n" . getSampleAuthor()->firstName;
    $lastName = "!.,:;\n" . getSampleAuthor()->lastName;

    setTextFieldValue('firstName', $firstName);
    setTextFieldValue('lastName', $lastName);

    clickButton('submitButton');

    assertPageContainsText($firstName);
    assertPageContainsText($lastName);
}

stf\runTests();
