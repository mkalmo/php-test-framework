<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);

function submitForm() {
    navigateTo('/form.html');

    setFieldValue('lastName', 'Smith');

    clickButton('submitButton');

    assertCurrentUrl(BASE_URL . "/params.php?a=1");

//    printPageSource();
}



stf\runTests();