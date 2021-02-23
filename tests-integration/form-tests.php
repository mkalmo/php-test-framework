<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);
//logRequests(true);

function submitForm() {
    navigateTo('/form.html');

    setTextFieldValue('lastName', 'Smith');

    clickButton('submitButton');

    assertCurrentUrl(BASE_URL . "/params.php?a=1");

//    printPageSource();
}



stf\runTests();