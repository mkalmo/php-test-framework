<?php

const MAX_POINTS = 5;
const RESULT_PATTERN = "\nRESULT: %s of %s POINTS\n";

function getPageId() : ?string {
    return stf\getGlobals()->page->getId();
}

function gotoLandingPage() {
    $landingPageUrl = stf\getGlobals()->baseUrl->asString();

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

function clickAuthorFormSubmitButton() {
    clickButton('submitButton');

    assertCorrectPageId('author-list-page');
}

function assertCorrectPageId($expectedPageId) {
    if (getPageId() !== $expectedPageId) {
        $message = sprintf("Page id should now be '%s' but was '%s'",
            $expectedPageId, getPageId());

        throw new stf\FrameworkException(ERROR_D01, $message);
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
    public string $isRead;
}

function getSampleAuthor() : Author {
    $author = new Author();
    $randomValue = substr(md5(mt_rand()), 0, 9);
    $author->firstName = $randomValue . '0';
    $author->lastName = $randomValue . '1';
    $author->grade = 5;
    return $author;
}

function getRandomString(int $length) : string {
    return substr(md5(mt_rand()), 0, $length);
}

function getSampleBook() : Book {
    $book = new Book();
    $randomValue = getRandomString(10);
    $book->title = $randomValue . '0';
    $book->grade = 5;
    $book->isRead = true;
    return $book;
}
