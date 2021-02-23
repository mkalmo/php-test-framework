<?php

require_once '../public-api.php';

const PROJECT_DIRECTORY = '/home/mkalmo/git/php/icd0007exphp';


function canSavePosts() {

    require_once 'ex8.php';

    $title = getRandomString(5);
    $text = getRandomString(10);

    $post = new Post($title, $text);

    savePost($post);

    assertContains(getAllPosts(), $post);
}

setIncludePath();

#Helpers

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
