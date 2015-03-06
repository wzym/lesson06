<?php

class News extends AbstractArticle {
    public $title;
    public $id;
    public $date;
    public $text;

    public static $table = 'news';
}