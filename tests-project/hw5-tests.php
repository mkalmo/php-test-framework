<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function displaysErrorWhenSubmittingInvalidBookData() {

    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setTextFieldValue('title', "aa");

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setTextFieldValue('title', "aaa");

    clickButton('submitButton');

    assertPageContainsElementWithId('message-block');
    assertPageDoesNotContainElementWithId('error-block');
}

function onValidationErrorDisplayedBookFormIsFilledWithInsertedData() {

    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    setTextFieldValue('title', "a");
    setRadioFieldValue('grade', "4");
    setCheckboxValue('isRead', true);

    clickButton('submitButton');

    assertThat(getFieldValue('title'), is('a'));
    assertThat(getFieldValue('grade'), is('4'));
    assertThat(getFieldValue('isRead'), is(true));
}

function displaysErrorWhenSubmittingInvalidAuthorData() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setTextFieldValue('firstName', "a");
    setTextFieldValue('lastName', "aa");

    clickButton('submitButton');

    assertPageContainsElementWithId('message-block');
    assertPageDoesNotContainElementWithId('error-block');
}

function onValidationErrorDisplayedAuthorFormIsFilledWithInsertedData() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    setTextFieldValue('firstName', "a");
    setTextFieldValue('lastName', "b");
    setRadioFieldValue('grade', "3");

    clickButton('submitButton');

    assertThat(getFieldValue('firstName'), is('a'));
    assertThat(getFieldValue('lastName'), is('b'));
    assertThat(getFieldValue('grade'), is('3'));
}

function canUpdateInsertedBooks() {

    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);
    setRadioFieldValue('grade', 5);
    setCheckboxValue('isRead', false);

    clickButton('submitButton');

    clickLinkWithText($book->title);

    assertThat(getFieldValue('title'), is($book->title));
    assertThat(getFieldValue('grade'), is('5'));
    assertThat(getFieldValue('isRead'), is(false));

    $updatedBook = getSampleBook();

    setTextFieldValue('title', $updatedBook->title);

    clickButton('submitButton');

    assertThat(getPageText(), containsString($updatedBook->title));
    assertThat(getPageText(), doesNotContainString($book->title));
}

function canUpdateInsertedAuthors() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);
    setRadioFieldValue('grade', '5');

    clickButton('submitButton');

    clickLinkWithText($author->firstName);

    assertThat(getFieldValue('firstName'), is($author->firstName));
    assertThat(getFieldValue('lastName'), is($author->lastName));
    assertThat(getFieldValue('grade'), is('5'));

    $updatedAuthor = getSampleAuthor();

    setTextFieldValue('firstName', $updatedAuthor->firstName);

    clickButton('submitButton');

    assertThat(getPageText(), containsString($updatedAuthor->firstName));
    assertThat(getPageText(), doesNotContainString($author->firstName));
}

function canDeleteInsertedBooks() {

    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    $book = getSampleBook();

    setTextFieldValue('title', $book->title);

    clickButton('submitButton');

    clickLinkWithText($book->title);

    clickButton('deleteButton');

    assertThat(getPageText(), doesNotContainString($book->title));
}

function canDeleteInsertedAuthors() {

    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);

    clickButton('submitButton');

    clickLinkWithText($author->firstName);

    clickButton('deleteButton');

    assertThat(getPageText(), doesNotContainString($author->firstName));
}

function bookFormsDeleteButtonIsNotVisibleWhenAddingNewBook() {
    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    assertPageDoesNotContainFieldWithName('deleteButton');
}

function authorFormsDeleteButtonIsNotVisibleWhenAddingNewAuthor() {
    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    assertPageDoesNotContainFieldWithName('deleteButton');
}

setBaseUrl(BASE_URL);
setLogRequests(false);
setLogPostParameters(false);
setPrintStackTrace(false);
setPrintPageSourceOnParseError(false);

stf\runTests();
