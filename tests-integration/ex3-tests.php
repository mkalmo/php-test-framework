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

function _canSendSimpleTextToDifferentReceiver() {
    navigateTo(BASE_URL . '/sender.html');

    setTextFieldValue('text', 'hello');

    clickButton('sendButton');

    printPageSource();
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
