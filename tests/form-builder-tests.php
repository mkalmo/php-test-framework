<?php

require_once '../public-api.php';

function buildsRadioButtons() {
    $html = '<form><input name="r1" type="radio" value="v1" />
                   <input name="r1" type="radio" value="v2" /></form>';

    $radio = getForm($html)->getRadioByName('r1');

    assertThat($radio->getValue(), is(''));

    $radio->selectOption('v1');

    assertThat($radio->getValue(), is('v1'));
}

#Helper
function getForm(string $html) : stf\Form {
    return (new stf\PageBuilder($html))->getPage()->getForm();
}

stf\runTests();