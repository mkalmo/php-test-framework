<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function submittingFormAddsBookToList() {
    gotoLandingPage();

    clickBookFormLink();

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);
    setRadioFieldValue('grade', $book->grade);
    setCheckboxValue('isRead', $book->isRead);

    clickBookFormSubmitButton();

    assertPageContainsText($book->title);
}

function submittingAuthorFormAddsAuthorToList() {
    gotoLandingPage();

    clickAuthorFormLink();

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);
    setRadioFieldValue('grade', $author->grade);

    clickAuthorFormSubmitButton();

    assertPageContainsText($author->firstName);
    assertPageContainsText($author->lastName);
}

function canHandleDifferentSymbolsInBookTitles() {

    gotoLandingPage();

    clickBookFormLink();

    $title = "!.,:;\n" . getSampleBook()->title;

    setTextFieldValue('title', $title);

    clickBookFormSubmitButton();

    assertPageContainsText($title);
}

function canHandleDifferentSymbolsInAuthorNames() {

    gotoLandingPage();

    clickAuthorFormLink();

    $firstName = "!.,:;\n" . getSampleAuthor()->firstName;
    $lastName = "!.,:;\n" . getSampleAuthor()->lastName;

    setTextFieldValue('firstName', $firstName);
    setTextFieldValue('lastName', $lastName);

    clickAuthorFormSubmitButton();

    assertPageContainsText($firstName);
    assertPageContainsText($lastName);
}

setBaseUrl(BASE_URL);
setLogRequests(false);
setLogPostParameters(false);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);

stf\runTests(new stf\PointsReporter([4 => 5]));
