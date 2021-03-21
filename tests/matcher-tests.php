<?php

require_once '../public-api.php';

function containsMatcher() {
    $text = 'abc 123 dcf';

    assertThat($text, containsString('123'));

    assertThrows(function () use ($text) {
        assertThat($text, containsString('123a'));
    });
}

function doesNotContainStringMather() {
    $text = 'abc 123 dcf';

    assertThat($text, doesNotContainString('123a'));

    assertThrows(function () use ($text) {
        assertThat($text, doesNotContainString('123'));
    });
}

function isMatcher() {
    $text = 'abc';

    assertThat($text, is('abc'));

    assertThrows(function () use ($text) {
        assertThat($text, is('ab'));
    });
}

function containsOnceMatcher() {
    $text = 'abcb';

    assertThat($text, containsStringOnce('a'));

    assertThrows(function () use ($text) {
        assertThat($text, containsStringOnce('b'));
    });
}

function containsAllowingHtmlSpecialCharsMatcher() {
    $text1 = '< \' " >';
    $text2 = '&lt; \' " &gt;';
    $text3 = '&lt; &apos; &quot; &gt;';
    $text4 = '&lt; &#039; &quot; &gt;';

    assertThat($text1, containsAllowingHtmlSpecialChars($text1));
    assertThat($text2, containsAllowingHtmlSpecialChars($text1));
    assertThat($text3, containsAllowingHtmlSpecialChars($text1));
    assertThat($text4, containsAllowingHtmlSpecialChars($text1));

    assertThrows(function () {
        assertThat(
            '&lt; \' &quot; &gt;',
            containsAllowingHtmlSpecialChars('&lt; \' " &gt;'));
    });
}

stf\runTests();