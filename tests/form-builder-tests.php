<?php

require_once '../public-api.php';

function buildsRadioButtons() {
    $html = '<form><input name="r1" type="radio" value="v1" />
                   <input name="r1" type="radio" checked value="v2" />
                   <input name="r1" type="radio" value="v3" /></form>';

    $radio = getForm($html)->getRadioByName('r1');

    assertThat($radio->getValue(), is('v2'));

    $radio->selectOption('v1');

    assertThat($radio->getValue(), is('v1'));
}

function buildsCheckboxes() {
    $html = '<form><input name="c1" type="checkbox" value="v1" />
                   <input name="c2" type="checkbox" checked value="v2" /></form>';

    $c1 = getForm($html)->getCheckboxByName('c1');
    $c2 = getForm($html)->getCheckboxByName('c2');

    assertThat($c1->getValue(), is(''));
    assertThat($c2->getValue(), is('v2'));
}

function buildsButtons() {
    $html = '<form action="?cmd=0">
                   <input type="submit" name="b1" 
                          value="Button 1" 
                          formaction="?cmd=1" />
                   <button type="submit" name="b2" 
                           formaction="?cmd=2">Button 2</button></form>';

    $b1 = getForm($html)->getButtonByName('b1');

    assertThat($b1->getName(), is('b1'));
    assertThat($b1->getValue(), is('Button 1'));
    assertThat($b1->getFormAction(), is('?cmd=1'));

    $b2 = getForm($html)->getButtonByName('b2');
    assertThat($b2->getName(), is('b2'));
    assertThat($b2->getValue(), is(''));
    assertThat($b2->getFormAction(), is('?cmd=2'));

}

#Helper
function getForm(string $html) : stf\Form {
    return (new stf\PageBuilder($html))->getPage()->getForm();
}

stf\runTests();