<?php

require_once '../public-api.php';

function buildPageSimple() {
    $html = '<a id="link1"> abc</a>';

    $page = (new stf\PageBuilder($html))->getPage();

    $link = $page->getLinkById('link1');

    assertThat($link->getText(), is(' abc'));
}

function buildPage() {

    $html = file_get_contents('../test-files/form.html');

    $page = (new stf\PageBuilder($html))->getPage();

    assertThat($page->getId(), is('form-page-id'));
}

function buildForm() {

    $html = file_get_contents('../test-files/form.html');

    $page = (new stf\PageBuilder($html))->getPage();

    assertThat($page->containsForm(), is(true));

    $form = $page->getForm();

    assertThat($form->getMethod(), is('post'));
    assertThat($form->getAction(), is('params.php?a=1'));
}

stf\runTests(new stf\PointsReporter([]));