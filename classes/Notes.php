<?php

class Notes extends AbstractArticle {
    public $author;
    public $note;
    public $date;
    public $id;

    public static $table = 'notes';
}