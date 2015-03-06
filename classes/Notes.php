<?php

class Notes extends AbstractArticle {
    public $author;
    public $note;
    public $date;

    public static $table = 'notes';
}