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
    assertThat(path('/a/')->asString(), is('/a'));
    assertThat(path('a/')->asString(), is('a'));
}

function asAbsolute() {
    assertThat(path('')->asAbsolute()->asString(), is('/'));

    assertThat(path('a/')->asAbsolute()->asString(), is('/a'));
    assertThat(path('/a')->asAbsolute()->asString(), is('/a'));
    assertThat(path('/')->asAbsolute()->asString(), is('/'));
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
}

#Helpers

function path(?string $path) : Path2 {
    return new Path2($path);
}

stf\runTests();