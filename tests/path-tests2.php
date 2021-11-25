<?php

require_once '../public-api.php';

use stf\browser\Path2;

function absolute() {

    assertThat(path('/')->isAbsolute(), is(true));

    assertThat(path('a')->isAbsolute(), is(false));

    assertThat(path('/a')->isAbsolute(), is(true));

    assertThat(path('a/')->isAbsolute(), is(false));
}

function asString() {
    assertThat(path('')->asString(), is(''));
    assertThat(path('a')->asString(), is('a'));
    assertThat(path('/')->asString(), is('/'));
    assertThat(path('/a')->asString(), is('/a'));
    assertThat(path('/a/')->asString(), is('/a/'));
    assertThat(path('a/')->asString(), is('a/'));
}

function normalize() {
    assertThat(Path2::normalize(path(''))->asString(), is(''));
    assertThat(Path2::normalize(path('.'))->asString(), is(''));
    assertThat(Path2::normalize(path('./'))->asString(), is(''));
    assertThat(Path2::normalize(path('..'))->asString(), is(''));
    assertThat(Path2::normalize(path('../'))->asString(), is(''));
}

function cd() {
    assertThat(path('')->cd(path(''))->asString(), is(''));
    assertThat(path('a')->cd(path(''))->asString(), is('a'));
    assertThat(path('/')->cd(path(''))->asString(), is('/'));

    assertThat(path('')->cd(path('/a'))->asString(), is('/a'));
    assertThat(path('/')->cd(path('/a'))->asString(), is('/a'));
    assertThat(path('a')->cd(path('/b'))->asString(), is('/b'));

    assertThat(path('a')->cd(path('b'))->asString(), is('a/b'));
    assertThat(path('/a')->cd(path('b'))->asString(), is('/a/b'));

    assertThat(path('')->cd(path('.'))->asString(), is(''));
    assertThat(path('/')->cd(path('.'))->asString(), is('/'));
    assertThat(path('a')->cd(path('.'))->asString(), is('a'));

    assertThat(path('a/')->cd(path('.'))->asString(), is('a/'));
    assertThat(path('a/')->cd(path('./'))->asString(), is('a/'));
    assertThat(path('/a')->cd(path('..'))->asString(), is('/'));
    assertThat(path('/a')->cd(path('../'))->asString(), is('/'));
}

#Helpers

function path(?string $path) : Path2 {
    return new Path2($path);
}

stf\runTests();