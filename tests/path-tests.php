<?php

include_once '../public-api.php';
include_once '../browser/Path.php';

use stf\Path;

function absoluteOrDirectory() {
    assertThat(path('/')->isAbsolute(), is(true));
    assertThat(path('/')->isDirectory(), is(true));

    assertThat(path('a')->isAbsolute(), is(false));
    assertThat(path('a')->isDirectory(), is(false));

    assertThat(path('/a')->isAbsolute(), is(true));
    assertThat(path('/a')->isDirectory(), is(false));

    assertThat(path('a/')->isAbsolute(), is(false));
    assertThat(path('a/')->isDirectory(), is(true));
}

function asAbsolute() {
    assertThat(path(null)->asAbsolute()->asString(), is(''));

    assertThat(path('/')->asAbsolute()->asString(), is('/'));
    assertThat(path('/a')->asAbsolute()->asString(), is('/a'));
    assertThat(path('/a/')->asAbsolute()->asString(), is('/a/'));

    assertThat(path('a/')->asAbsolute()->asString(), is('/a/'));
}

function extend() {
    assertThat(path(null)->extend(path(null))->asString(), is(''));
    assertThat(path('')->extend(path(null))->asString(), is(''));
    assertThat(path('a')->extend(path(null))->asString(), is('a'));
    assertThat(path('a/')->extend(path(null))->asString(), is('a/'));
    assertThat(path('/')->extend(path(null))->asString(), is('/'));

    assertThat(path(null)->extend(path('/a'))->asString(), is('/a'));
    assertThat(path('/')->extend(path('/a'))->asString(), is('/a'));
    assertThat(path('a')->extend(path('/b'))->asString(), is('/b'));

    assertThat(path('a')->extend(path('b'))->asString(), is('a/b'));
    assertThat(path('/a')->extend(path('b'))->asString(), is('/a/b'));
    assertThat(path('/a/')->extend(path('b'))->asString(), is('/a/b'));
    assertThat(path('/a')->extend(path('b/'))->asString(), is('/a/b/'));
}

function removeFilePart() {
    assertThat(path('a')->removeFilePart()->asString(), is(''));
    assertThat(path('')->removeFilePart()->asString(), is(''));
    assertThat(path('/a')->removeFilePart()->asString(), is('/'));
    assertThat(path('/a/')->removeFilePart()->asString(), is('/a/'));
    assertThat(path('/a/b')->removeFilePart()->asString(), is('/a/'));
}

function normalize() {

    assertThat(path('/.')->normalize()->asString(), is('/'));
    assertThat(path('/a/../b')->normalize()->asString(), is('/b'));
    assertThat(path('/a/../../b')->normalize()->asString(), is('/b'));

    assertThrows(function () {
        path('../b')->normalize();
    });

}

#Helper
function path(?string $path) : Path {
    return new Path($path);
}

stf\runTests();