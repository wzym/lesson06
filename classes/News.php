<?php

class News extends AbstractArticle {
    public $title;
    public $date;
    public $text;

    public static $table = 'news';
}