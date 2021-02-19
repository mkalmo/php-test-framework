<?php

const MAX_POINTS = 5;
const RESULT_PATTERN = "\nRESULT: %s of %s POINTS\n";

function getPageId() : ?string {
    return stf\getGlobals()->page->getId();
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

function getSampleBook() : Book {
    $book = new Book();
    $randomValue = substr(md5(mt_rand()), 0, 9);
    $book->title = $randomValue . '0';
    $book->grade = 5;
    $book->isRead = true;
    return $book;
}
