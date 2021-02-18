<?php

require_once '../public-api.php';

function radioGroupTest() {

    $radio = new stf\RadioGroup('r1');

    $radio->addOption("v1");
    $radio->addOption("v2");

    assertThat($radio->getValue(), is(''));

    $radio->selectOption("v1");

    assertThat($radio->getValue(), is('v1'));
}

stf\runTests();