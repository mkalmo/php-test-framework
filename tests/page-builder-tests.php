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

stf\runTests();