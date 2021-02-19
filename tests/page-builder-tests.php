<?php

require_once '../public-api.php';

function buildPageSimple() {
    $html = '<a id="link1"> abc</a>';

    $page = getPage($html);

    $link = $page->getLinkById('link1');

    assertThat($link->getText(), is(' abc'));
}

function buildPage() {

    $html = file_get_contents('../test-files/form.html');

    $page = getPage($html);

    assertThat($page->getId(), is('form-page-id'));
}

#Helper
function getPage(string $html) : stf\Page {
    $parser = new stf\PageParser($html);

    return (new stf\PageBuilder($html, $parser->getNodeTree()))->getPage();
}

stf\runTests();