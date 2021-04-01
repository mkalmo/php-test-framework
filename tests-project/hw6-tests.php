<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function canSaveBooksWithSingleAuthor() {

    $authorName = insertSampleAuthor();

    gotoLandingPage();

    clickBookFormLink();

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);
    setRadioFieldValue('grade', '5');
    selectOptionWithText('author1', $authorName);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsStringOnce($book->title));
    assertThat(getPageText(), containsStringOnce($authorName));
}

#Helpers

function insertSampleAuthor() : string {

    gotoLandingPage();

    clickAuthorFormLink();

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);

    clickAuthorFormSubmitButton();

    return $author->firstName . ' ' . $author->lastName;
}


setBaseUrl(BASE_URL);
setLogRequests(false);
setLogPostParameters(true);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);

stf\runTests(new stf\PointsReporter([10 => 5]));
