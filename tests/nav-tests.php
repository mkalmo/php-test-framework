<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

setBaseUrl(BASE_URL);

function indexToA() {
    navigateTo('/');

    clickLinkByText("a.html");

    assertCurrentUrl(BASE_URL . "/a/a.html");
}

function aToE() {
    navigateTo('/a/a.html');

    clickLinkByText("e.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/e.html");
}

function eToD() {
    navigateTo('/a/b/c/d/e/e.html');

    clickLinkByText("d.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/d.html");
}

function dToB() {
    navigateTo('/a/b/c/d/d.html');

    clickLinkByText("b.html");

    assertCurrentUrl(BASE_URL . "/a/b/b.html");
}

function emptyLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkByText("shortest self");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/f/f.html");
}

function directoryLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkByText("shortest f/index.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/f/");
}

function rootLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkByText("shortest a.html");

    assertCurrentUrl(BASE_URL . "/a/a.html");
}

stf\runTests();