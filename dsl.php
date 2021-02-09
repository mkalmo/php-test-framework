<?php

class Author {
    public $firstName;
    public $lastName;
    public $grade;
}

class Book {
    public $title;
    public $grade;
    public $isRead;
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