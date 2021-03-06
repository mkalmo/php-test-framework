<?php

require_once '../public-api.php';

const BASE_URL = 'http://localhost:8080';

function indexToA() {
    navigateTo('/');

    clickLinkWithText("a.html");

    assertCurrentUrl(BASE_URL . "/a/a.html");
}

function aToE() {
    navigateTo('/a/a.html');

    clickLinkWithText("e.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/e.html");
}

function eToD() {
    navigateTo('/a/b/c/d/e/e.html');

    clickLinkWithText("d.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/d.html");
}

function dToB() {
    navigateTo('/a/b/c/d/d.html');

    clickLinkWithText("b.html");

    assertCurrentUrl(BASE_URL . "/a/b/b.html");
}

function emptyLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkWithText("shortest self");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/f/f.html");
}

function directoryLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkWithText("shortest f/index.html");

    assertCurrentUrl(BASE_URL . "/a/b/c/d/e/f/");
}

function rootLink() {
    navigateTo('/a/b/c/d/e/f/f.html');

    clickLinkWithText("shortest a.html");

    assertCurrentUrl(BASE_URL . "/a/a.html");
}

function redirect() {
    navigateTo('/redirect.php');

    assertCurrentUrl(BASE_URL . "/redirect.php?count=0");
}

setBaseUrl(BASE_URL);
setLogRequests(false);
setLogPostParameters(false);
setPrintStackTrace(false);
setPrintPageSourceOnError(false);


stf\runTests();