<?php

require_once '../public-api.php';

use stf\browser\page\PageParser;
use stf\browser\page\PageBuilder;
use stf\browser\page\Form;
use stf\browser\page\NodeTree;

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

function buildsSelect() {
    $html = "<form>
             <select name='s1'>
             <OPTION value='v1'> \n Value 1 \n </OPTION>
             <option selected value='v2'> \n Value 2 \n </option>
             <option value='v3'> \n Value 3 \n </option>
             </select>
             </form>";

    $select = getForm($html)->getSelectByName('s1');

    assertThat($select->getName(), is('s1'));

    assertThat($select->hasOptionWithLabel('Value 1'), is(true));
    assertThat($select->hasOptionWithLabel('Value 2'), is(true));
    assertThat($select->hasOptionWithLabel('Value 3'), is(true));
    assertThat($select->hasOptionWithLabel('Value 4'), is(false));

    assertThat($select->getValue(), is('v2'));
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

function buildsButtonsWithValue() {
    $html = '<form>
             <button type="submit" name="cmd" 
                     value="c1">Cmd 1</button>
             <button type="submit" name="cmd" 
                     value="c2">Cmd 2</button></form>';

    $button = getForm($html)->getButtonByNameAndValue('cmd', 'c1');

    assertThat($button->getName(), is('cmd'));
    assertThat($button->getValue(), is('c1'));
}

function buildsTextArea() {
    $html = '<form><textarea name="a1"> Hello </textarea></form>';

    $field = getForm($html)->getTextFieldByName('a1');

    assertThat($field->getName(), is('a1'));
    assertThat($field->getValue(), is(' Hello '));
}

#Helpers

function getForm(string $html) : Form {
    $parser = new PageParser($html);

    $nodeTree = new NodeTree($parser->getNodeTree());

    return (new PageBuilder($nodeTree, $html))->getPage()->getForm();
}

stf\runTests();