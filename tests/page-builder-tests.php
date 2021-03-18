<?php

require_once '../public-api.php';

use stf\browser\page\NodeTree;
use stf\browser\page\PageParser;
use stf\browser\page\PageBuilder;
use stf\browser\page\Page;

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

#Helpers

function getPage(string $html) : Page {
    $parser = new PageParser($html);

    $nodeTree = new NodeTree($parser->getNodeTree());

    return (new PageBuilder($nodeTree, $html))->getPage();
}

stf\runTests();