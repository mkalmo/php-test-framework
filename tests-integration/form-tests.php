<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function submitForm() {
    navigateTo('/form.html');

    setTextFieldValue('lastName', 'Smith');

    clickButton('submitButton');

    assertCurrentUrl(BASE_URL . "/params.php?a=1");

//    printPageSource();
}

setBaseUrl(BASE_URL);
setLogRequests(true);
setLogPostParameters(true);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);


stf\runTests();