<?php

require_once '../public-api.php';
require_once '../dsl.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);
logRequests(true);
logPostParameters(true);

function displaysErrorWhenSubmittingInvalidBookData() {

    navigateTo('/');

    clickLinkById('book-form-link');

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setFieldValue('title', "aa");

    clickButton('submitButton');

    assertPageContainsElementWithId('error-block');

    setFieldValue('title', "aaa");

    clickButton('submitButton');

    // $this->assertNoElementById('error-block');

    // assertThat(page(), not(contains(element(withId('error-block')))));

    assertPageContainsElementWithId('message-block');
}

function _onValidationErrorDisplayedBookFormIsFilledWithInsertedData() {

    navigateTo('/');

    clickLinkById('book-form-link');

    setFieldValue('title', "a");
    setFieldValue('grade', "4");
    setFieldValue('isRead', true);

    clickButton('submitButton');

    assertFieldValue('title', "a");
    assertFieldValue('grade', "4");
    assertFieldValue('isRead', true);
}


stf\runTests();
