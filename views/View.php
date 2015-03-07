<?php

class View implements Iterator {
    private $content;     // поле для хранения адреса вложенной вьюшки
    private $tempContent;
    public $infoToShow = [];

    public function __set($key, $value) {
        $this->infoToShow[$key] = $value;
    }

    public function __get($key) {
        return $this->infoToShow[$key];
    }

    public function __construct($content = '/main.html') {      // При создании объекта вьюшки определяемся с контентом.
        $this->content = $content;
    }

    public function render() {
        ob_start();
        include 'index.html';
        $this->tempContent = ob_get_contents();
        ob_end_clean();
    }

    public function display() {
        echo $this->tempContent;
    }

    public function current() {
        return current($this->infoToShow);
    }

    public function next() {
        return next($this->infoToShow);
    }

    public function key() {
        return key($this->infoToShow);
    }

    public function valid() {
        return isset($this->infoToShow[$this->key()]);
    }

    public function rewind() {
        return reset($this->infoToShow);
    }
}