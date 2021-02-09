<?php

require_once '../public-api.php';

function buildForm() {

    $html = file_get_contents('../test-files/form.html');

    $page = (new stf\PageBuilder($html))->getPage();

    assertThat($page->containsForm(), is(true));

    $form = $page->getForm();

    assertThat($form->getMethod(), is('post'));
    assertThat($form->getAction(), is('params.php?a=1'));
}

stf\runTests();