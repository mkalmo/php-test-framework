<?php

require_once '../public-api.php';

use stf\browser\Url;

function requestParameters() {
    $url = url('http://db.lh/a.php?a=1');

    assertThat($url->asString(), is('http://db.lh/a.php?a=1'));
    assertThat($url->getQueryString(), is('a=1'));

    $url->addRequestParameter('b','2');

    assertThat($url->getQueryString(), is('a=1&b=2'));

    $url = url('http://db.lh/a.php');

    assertThat($url->asString(), is('http://db.lh/a.php'));
    assertThat($url->getQueryString(), is(''));

    $url->addRequestParameter('b','2');

    assertThat($url->asString(), is('http://db.lh/a.php?b=2'));
    assertThat($url->getQueryString(), is('b=2'));
}

function asString() {
    assertThat(url('http://lh')->asString(), is('http://lh'));

    assertThat(url('http://db.lh')->asString(), is('http://db.lh'));
}

function handlesNull() {
    assertThat(url('http://lh')->navigateTo(null)->asString(), is('http://lh'));
}

function hostname() {
    assertThat(url('http://lh')->navigateTo('')->asString(), is('http://lh'));
    assertThat(url('http://lh')->navigateTo('.')->asString(), is('http://lh'));
    assertThat(url('http://lh')->navigateTo('./')->asString(), is('http://lh'));
    assertThat(url('http://lh')->navigateTo('/')->asString(), is('http://lh'));

    assertThat(url('http://lh')->navigateTo('/../../')->asString(), is('http://lh'));
    assertThat(url('http://lh')->navigateTo('/../../.')->asString(), is('http://lh'));

    assertThat(url('http://lh')->navigateTo('/../a')->asString(), is('http://lh/a'));
}

function hostnameSlash() {
    assertThat(url('http://lh/')->navigateTo('')->asString(), is('http://lh'));
    assertThat(url('http://lh/')->navigateTo('.')->asString(), is('http://lh'));
    assertThat(url('http://lh/')->navigateTo('./')->asString(), is('http://lh'));
    assertThat(url('http://lh/')->navigateTo('/')->asString(), is('http://lh'));

    assertThat(url('http://lh/')->navigateTo('/../../')->asString(), is('http://lh'));
    assertThat(url('http://lh/')->navigateTo('/../../.')->asString(), is('http://lh'));

    assertThat(url('http://lh/')->navigateTo('/../a')->asString(), is('http://lh/a'));
}

function fromFile() {
    assertThat(url('http://lh/a')->navigateTo('')->asString(), is('http://lh/a'));
    assertThat(url('http://lh/a')->navigateTo('.')->asString(), is('http://lh'));
    assertThat(url('http://lh/a')->navigateTo('./')->asString(), is('http://lh'));
    assertThat(url('http://lh/a')->navigateTo('b')->asString(), is('http://lh/b'));
}

function fromDir() {
    assertThat(url('http://lh/a/')->navigateTo('')->asString(), is('http://lh/a/'));
    assertThat(url('http://lh/a/')->navigateTo('.')->asString(), is('http://lh/a/'));
    assertThat(url('http://lh/a/')->navigateTo('./')->asString(), is('http://lh/a/'));

    assertThat(url('http://lh/a/')->navigateTo('/')->asString(), is('http://lh'));

    assertThat(url('http://lh/a/')->navigateTo('b')->asString(), is('http://lh/a/b'));
}

#Helpers

function url(?string $url) : Url {
    return new Url($url);
}

stf\runTests();