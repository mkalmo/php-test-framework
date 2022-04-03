<?php

const MAX_POINTS = 4;
const RESULT_PATTERN = "\nRESULT: %s of %s points\n";

function getPageId() : ?string {
    return getBrowser()->getPageId();
}

function gotoLandingPage() {
    $landingPageUrl = getGlobals()->baseUrl->asString();

    navigateTo($landingPageUrl);

    assertCorrectPageId('book-list-page');
}

function clickBookFormLink() {
    clickLinkWithId('book-form-link');

    assertCorrectPageId('book-form-page');
}

function clickAuthorFormLink() {
    clickLinkWithId('author-form-link');

    assertCorrectPageId('author-form-page');
}

function clickBookFormSubmitButton() {
    clickButton('submitButton');

    assertCorrectPageId('book-list-page');
}

function clickBookFormDeleteButton() {
    clickButton('deleteButton');

    assertCorrectPageId('book-list-page');
}

function clickAuthorFormSubmitButton() {
    clickButton('submitButton');

    assertCorrectPageId('author-list-page');
}

function clickAuthorFormDeleteButton() {
    clickButton('deleteButton');

    assertCorrectPageId('author-list-page');
}

function assertCorrectPageId($expectedPageId) {
    if (getPageId() !== $expectedPageId) {
        $message = sprintf("Page id should now be '%s' but was '%s'",
            $expectedPageId, getPageId());

        throw new stf\FrameworkException(ERROR_D01, $message);
    }
}

function assertContains(array $allPosts, Post $post) {
    foreach ($allPosts as $each) {
        if ($each->title === $post->title && $each->text === $post->text) {
            return;
        }
    }

    throw new stf\FrameworkException(ERROR_C01, "Did not find saved post");
}

function assertDoesNotContainPostWithTitle(array $allPosts, string $title) {
    foreach ($allPosts as $each) {
        if ($each->title === $title) {
            throw new stf\FrameworkException(ERROR_C01,
                sprintf("Found post with title '%s'", $title));
        }
    }
}

class Author {
    public string $firstName;
    public string $lastName;
    public string $grade;
}

class Book {
    public string $title;
    public string $grade;
    public bool $isRead;
}

function getSampleAuthor() : Author {
    $author = new Author();
    $author->firstName = getRandomString(3) . ' ' . getRandomString(4);
    $author->lastName = getRandomString(5) . ' ' . getRandomString(3);
    $author->grade = 4;
    return $author;
}

function getRandomString(int $length) : string {
    return substr(md5(mt_rand()), 0, $length);
}

function getSampleBook() : Book {
    $book = new Book();
    $book->title = getRandomString(5) . ' ' . getRandomString(5);
    $book->grade = 5;
    $book->isRead = true;
    return $book;
}

function insertSampleAuthor() : string {

    gotoLandingPage();

    clickAuthorFormLink();

    $author = getSampleAuthor();

    setTextFieldValue('firstName', $author->firstName);
    setTextFieldValue('lastName', $author->lastName);

    clickAuthorFormSubmitButton();

    return $author->firstName . ' ' . $author->lastName;
}
