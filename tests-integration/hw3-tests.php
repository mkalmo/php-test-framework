<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);

function bookListPageContainsCorrectMenu() {
    navigateTo(BASE_URL);

    assertThat(getPageId(), is('book-list-page'));

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');
}

function bookFormPageContainsCorrectElements() {
    navigateTo(BASE_URL);

    clickLinkWithId('book-form-link');

    assertThat(getPageId(), is('book-form-page'));

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
    navigateTo(BASE_URL);

    clickLinkWithId('author-list-link');

    assertThat(getPageId(), is('author-list-page'));

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');
}

function authorFormPageContainsCorrectElements() {
    navigateTo(BASE_URL);

    clickLinkWithId('author-form-link');

    assertThat(getPageId(), is('author-form-page'));

    assertPageContainsLinkWithId('book-list-link');
    assertPageContainsLinkWithId('book-form-link');
    assertPageContainsLinkWithId('author-list-link');
    assertPageContainsLinkWithId('author-form-link');

    assertPageContainsTextFieldWithName('firstName');
    assertPageContainsTextFieldWithName('lastName');
    assertPageContainsRadioWithName('grade');
    assertPageContainsButtonWithName('submitButton');
}

stf\runTests();
