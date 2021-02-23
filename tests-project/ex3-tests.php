<?php

require_once '../public-api.php';

const PROJECT_DIRECTORY = '/home/mkalmo/git/php/icd0007exphp';
const BASE_URL = 'http://localhost:8080';

function canSavePosts() {

    require_once 'ex8.php';

    $title = getRandomString(5);
    $text = getRandomString(10);

    $post = new Post($title, $text);

    savePost($post);

    assertContains(getAllPosts(), $post);
}

function canSavePostsContainingDifferentSymbols() {

    require_once 'ex8.php';

    $title = getRandomString(5);
    $text = getRandomString(10) . ".'\n;";

    $post = new Post($title, $text);

    savePost($post);

    assertContains(getAllPosts(), $post);
}

function canSendSimpleTextToDifferentReceiver() {
    navigateTo(BASE_URL . '/flow/sender.html');

    setTextFieldValue('text', 'hello');

    clickButton('sendButton');

    assertPageContainsText('hello');
}

function canSendMultilineTextToDifferentReceiver() {
    navigateTo(BASE_URL . '/flow/sender.html');

    setTextFieldValue('text', "hello\nworld");

    clickButton('sendButton');

    assertPageContainsText("hello\nworld");
}

function canSendSimpleTextThroughRedirect() {
    navigateTo(BASE_URL . '/flow/sender.php');

    setTextFieldValue('text', 'hello');

    clickButton('sendButton');

    assertPageContainsText('hello');
}

function canSendMultilineTextThroughRedirect() {
    navigateTo(BASE_URL . '/flow/sender.php');

    setTextFieldValue('text', "hello\nworld");

    clickButton('sendButton');

    assertPageContainsText("hello\nworld");
}

function landingPageHasMenuWithCorrectLinks() {
    navigateTo(BASE_URL . '/calc/');

    assertPageContainsLinkWithId('c2f');
    assertPageContainsLinkWithId('f2c');
}

function f2cPageHasMenuWithCorrectLinks() {
    navigateTo(BASE_URL . '/calc/');

    clickLinkWithId('f2c');

    assertPageContainsLinkWithId('c2f');
    assertPageContainsLinkWithId('f2c');
}

function calculatesCelsiusToFahrenheit() {
    navigateTo(BASE_URL . '/calc/');

    setTextFieldValue('temperature', '20');

    clickButton('calculateButton');

    assertPageContainsText('is 68 decrees');
}

function calculatesFahrenheitToCelsius() {
    navigateTo(BASE_URL . '/calc/');

    clickLinkWithId('f2c');

    setTextFieldValue('temperature', '68');

    clickButton('calculateButton');

    assertPageContainsText('is 20 decrees');
}

#Helpers

setBaseUrl(BASE_URL);

setIncludePath();

function assertContains(array $allPosts, Post $post) {
    foreach ($allPosts as $each) {
        if ($each->title === $post->title && $each->text === $post->text) {
            return;
        }
    }

    throw new stf\FrameworkException(ERROR_C01, "Did not find saved post");
}

function setIncludePath() {
    set_include_path(get_include_path() . PATH_SEPARATOR . getProjectDirectory());
}

function getProjectDirectory() : string {
    global $argc, $argv;

    $path = $argc === 2 ? $path = $argv[1] : PROJECT_DIRECTORY;

    if (!$path) {
        die("Please specify your projects directory in constant PROJECT_DIRECTORY");
    }

    $path = realpath($path);

    if (!file_exists($path)) {
        die("Value in PROJECT_DIRECTORY is not correct directory");
    }

    return $path;
}

stf\runTests();
