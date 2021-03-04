<?php

require_once '../public-api.php';

use stf\browser\Url;

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