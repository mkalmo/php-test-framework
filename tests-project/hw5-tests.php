<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function displaysErrorWhenSubmittingInvalidBookData() {

    gotoLandingPage();

    clickBookFormLink();

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setTextFieldValue('title', "aaa");

    clickBookFormSubmitButton();

    assertPageContainsElementWithId('message-block');
    assertPageDoesNotContainElementWithId('error-block');
}

function onValidationErrorDisplayedBookFormIsFilledWithInsertedData() {

    gotoLandingPage();

    clickBookFormLink();

    setTextFieldValue('title', 'a');
    setRadioFieldValue('grade', '4');
    setCheckboxValue('isRead', true);

    clickButton('submitButton');

    assertThat(getFieldValue('title'), is('a'));
    assertThat(getFieldValue('grade'), is('4'));
    assertThat(getFieldValue('isRead'), is(true));
}

function displaysErrorWhenSubmittingInvalidAuthorData() {

    gotoLandingPage();

    clickAuthorFormLink();

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setTextFieldValue('firstName', 'a');
    setTextFieldValue('lastName', 'aa');

    clickButton('submitButton');

    assertPageDoesNotContainElementWithId('error-block');
    assertPageContainsElementWithId('message-block');
}

function onValidationErrorDisplayedAuthorFormIsFilledWithInsertedData() {

    gotoLandingPage();

    clickAuthorFormLink();

    setTextFieldValue('firstName', 'a');
    setTextFieldValue('lastName', 'b');
    setRadioFieldValue('grade', '3');

    clickButton('submitButton');

    assertThat(getFieldValue('firstName'), is('a'));
    assertThat(getFieldValue('lastName'), is('b'));
    assertThat(getFieldValue('grade'), is('3'));
}

function canUpdateInsertedBooks() {

    gotoLandingPage();

    clickBookFormLink();

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);
    setRadioFieldValue('grade', '5');
    setCheckboxValue('isRead', false);

    clickBookFormSubmitButton();

    clickLinkWithText($book->title);

    assertThat(getFieldValue('title'), is($book->title));
    assertThat(getFieldValue('grade'), is('5'));
    assertThat(getFieldValue('isRead'), is(false));

    $updatedBook = getSampleBook();

    setTextFieldValue('title', $updatedBook->title);

    clickBookFormSubmitButton();

    assertThat(getPageText(), containsString($updatedBook->title));
    assertThat(getPageText(), doesNotContainString($book->title));
}

function canUpdateInsertedAuthors() {

    gotoLandingPage();

    clickAuthorFormLink();

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);
    setRadioFieldValue('grade', '2');

    clickAuthorFormSubmitButton();

    clickLinkWithText($author->firstName);

    assertThat(getFieldValue('firstName'), is($author->firstName));
    assertThat(getFieldValue('lastName'), is($author->lastName));
    assertThat(getFieldValue('grade'), is('2'));

    $updatedAuthor = getSampleAuthor();

    setTextFieldValue('firstName', $updatedAuthor->firstName);

    clickAuthorFormSubmitButton();

    assertThat(getPageText(), containsString($updatedAuthor->firstName));
    assertThat(getPageText(), doesNotContainString($author->firstName));
}

function canDeleteInsertedBooks() {

    gotoLandingPage();

    clickBookFormLink();

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);

    clickBookFormSubmitButton();

    clickLinkWithText($book->title);

    clickBookFormDeleteButton();

    assertThat(getPageText(), doesNotContainString($book->title));
}

function canDeleteInsertedAuthors() {

    gotoLandingPage();

    clickAuthorFormLink();

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);

    clickAuthorFormSubmitButton();

    clickLinkWithText($author->firstName);

    clickAuthorFormDeleteButton();

    assertThat(getPageText(), doesNotContainString($author->firstName));
}

function bookFormsDeleteButtonIsNotVisibleWhenAddingNewBook() {

    gotoLandingPage();

    clickBookFormLink();

    assertPageDoesNotContainButtonWithName('deleteButton');
}

function authorFormsDeleteButtonIsNotVisibleWhenAddingNewAuthor() {

    gotoLandingPage();

    clickAuthorFormLink();

    assertPageDoesNotContainButtonWithName('deleteButton');
}

setBaseUrl(BASE_URL);
setLogRequests(false);
setLogPostParameters(false);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);

stf\runTests(new stf\PointsReporter([10 => 5]));
