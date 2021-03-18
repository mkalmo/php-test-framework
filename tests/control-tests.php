<?php

require_once '../public-api.php';

use stf\browser\page\RadioGroup;
use stf\browser\page\Checkbox;
use stf\browser\page\Select;

function radioGroupTest() {

    $radio = new RadioGroup('r1');

    $radio->addOption("v1");
    $radio->addOption("v2");

    assertThat($radio->getValue(), is(''));

    $radio->selectOption("v1");

    assertThat($radio->getValue(), is('v1'));

    assertThat($radio->hasOption('v1'), is(true));
    assertThat($radio->hasOption('v2'), is(true));
    assertThat($radio->hasOption('v3'), is(false));
}

function selectTest() {

    $select = new Select('s1');

    $select->addOption("v1", "Value 1");
    $select->addOption("v2", "Value 2");
    $select->addOption("v3", "Value 3");

    assertThat($select->getValue(), is(''));

    $select->selectOptionWithText("Value 2");

    assertThat($select->getValue(), is('v2'));

    assertThat($select->hasOptionWithLabel("Value 1"), is(true));
    assertThat($select->hasOptionWithLabel("Value 4"), is(false));
}

function checkboxTest() {
    $checkbox = new Checkbox('c1', 'on');

    assertThat($checkbox->isChecked(), is(false));
    assertThat($checkbox->getValue(), is(''));

    $checkbox->check(true);

    assertThat($checkbox->isChecked(), is(true));
    assertThat($checkbox->getValue(), is('on'));
}

stf\runTests();