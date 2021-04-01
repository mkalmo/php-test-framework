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

function canUpdateBooksWithSingleAuthor() {

    $originalAuthorName = insertSampleAuthor();
    $newAuthorName = insertSampleAuthor();

    gotoLandingPage();

    clickBookFormLink();

    $bookTitle = getSampleBook()->title;

    setTextFieldValue('title', $bookTitle);
    selectOptionWithText('author1', $originalAuthorName);

    clickBookFormSubmitButton();

    clickLinkWithText($bookTitle);

    assertThat(getFieldValue('title'), is($bookTitle));
    assertThat(getSelectedOptionText('author1'), is($originalAuthorName));

    selectOptionWithText('author1', $newAuthorName);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsStringOnce($newAuthorName));
    assertThat(getPageText(), doesNotContainString($originalAuthorName));
}

function canSaveBooksWithMultipleAuthors() {

    $authorName1 = insertSampleAuthor();
    $authorName2 = insertSampleAuthor();

    gotoLandingPage();

    clickBookFormLink();

    $bookTitle = getSampleBook()->title;

    setTextFieldValue('title', $bookTitle);
    selectOptionWithText('author1', $authorName1);
    selectOptionWithText('author2', $authorName2);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsStringOnce($bookTitle));
    assertThat(getPageText(), containsStringOnce($authorName1));
    assertThat(getPageText(), containsStringOnce($authorName2));
}

function canUpdateBooksWithMultipleAuthors() {

    $authorName1 = insertSampleAuthor();
    $authorName2 = insertSampleAuthor();
    $authorName3 = insertSampleAuthor();

    gotoLandingPage();

    clickBookFormLink();

    $bookTitle = getSampleBook()->title;

    setTextFieldValue('title', $bookTitle);
    selectOptionWithText('author1', $authorName1);
    selectOptionWithText('author2', $authorName2);

    clickBookFormSubmitButton();

    clickLinkWithText($bookTitle);

    assertThat(getFieldValue('title'), is($bookTitle));
    assertThat(getSelectedOptionText('author1'), is($authorName1));
    assertThat(getSelectedOptionText('author2'), is($authorName2));

    selectOptionWithText('author1', $authorName2);
    selectOptionWithText('author2', $authorName3);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsStringOnce($authorName2));
    assertThat(getPageText(), containsStringOnce($authorName3));
    assertThat(getPageText(), doesNotContainString($authorName1));
}

function doesNotAllowSqlInjectionWhenAddingBook() {

    gotoLandingPage();

    clickBookFormLink();

    $dangerousSymbols = " \" ' ";
    $bookTitle = getSampleBook()->title; // 1e549 f26a5
    $dangerousBookTitle = $bookTitle . $dangerousSymbols; // 1e549 f26a5 " '

    // should accept this value as a book title
    setTextFieldValue('title', $dangerousBookTitle);

    // should ignore these values and not break
    forceFieldValue('grade', $dangerousSymbols);
    forceFieldValue('isRead', $dangerousSymbols);
    forceFieldValue('author1', $dangerousSymbols);
    forceFieldValue('author2', $dangerousSymbols);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsString($dangerousBookTitle));
}

function doesNotAllowSqlInjectionWhenAddingAuthor() {

    gotoLandingPage();

    clickAuthorFormLink();

    $dangerousSymbols = " \" ' ";
    $firstName = getSampleAuthor()->firstName; // f4d 544a
    $lastName = getSampleAuthor()->lastName; // c3841 251

    $dangerousFirstName = $firstName . $dangerousSymbols; // f4d 544a " '
    $dangerousLastName = $lastName . $dangerousSymbols; // c3841 251 " '

    // should accept these values as names
    setTextFieldValue('firstName', $dangerousFirstName);
    setTextFieldValue('lastName', $dangerousLastName);

    // should ignore this value and not break
    forceFieldValue('grade', $dangerousSymbols);

    clickAuthorFormSubmitButton();

    assertThat(getPageText(), containsString($dangerousFirstName));
    assertThat(getPageText(), containsString($dangerousLastName));
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
setLogPostParameters(false);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);

stf\runTests(new stf\PointsReporter([4 => 3, 6 => 5]));
